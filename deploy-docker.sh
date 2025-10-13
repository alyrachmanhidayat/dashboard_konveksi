#!/bin/bash

# deploy-docker.sh - Automated deployment script for Dashboard Konveksi
# This script handles first-time setup including database migrations, seeding, and key generation

set -e  # Exit immediately if a command exits with a non-zero status

echo "==============================================="
echo " Dashboard Konveksi - Docker Deployment Script "
echo "==============================================="
echo ""

# Function to check if Docker is installed
check_docker() {
    if ! command -v docker &> /dev/null; then
        echo "❌ Error: Docker is not installed."
        echo "Please install Docker first: https://docs.docker.com/get-docker/"
        exit 1
    fi
    
    if ! command -v docker compose &> /dev/null; then
        echo "❌ Error: Docker Compose is not installed."
        echo "Please install Docker Compose: https://docs.docker.com/compose/install/"
        exit 1
    fi
    
    echo "✅ Docker and Docker Compose are installed"
}

# Function to check if containers are already running
check_running_containers() {
    if [ "$(docker compose ps -q 2>/dev/null | wc -l)" -gt 0 ]; then
        echo "⚠️  Existing containers detected. Stopping them..."
        docker compose down
        echo "✅ Previous containers stopped"
    fi
}

# Function to build and start containers
build_and_start() {
    echo ""
    echo "🐳 Building and starting Docker containers..."
    echo ""
    
    # Build and start containers
    docker compose up -d --build
    
    # Wait for containers to be healthy
    echo "⏳ Waiting for containers to start..."
    sleep 10
    
    # Check if containers are running
    if [ "$(docker compose ps -q | wc -l)" -eq 4 ]; then
        echo "✅ All containers started successfully"
    else
        echo "⚠️  Some containers may not have started correctly"
        docker compose ps
    fi
}

# Function to wait for database to be ready
wait_for_database() {
    echo ""
    echo "⏳ Waiting for database to be ready..."
    
    # Wait for MySQL to be ready
    timeout=60
    counter=0
    
    until docker compose exec db mysql -ularavel_user -plaravel_password -e "SELECT 1;" &> /dev/null || [ $counter -eq $timeout ]; do
        printf "."
        sleep 2
        counter=$((counter + 1))
    done
    
    if [ $counter -eq $timeout ]; then
        echo ""
        echo "❌ Database did not become ready in time"
        exit 1
    else
        echo ""
        echo "✅ Database is ready"
    fi
}

# Function to generate application key
generate_app_key() {
    echo ""
    echo "🔑 Generating application key..."
    
    # Check if APP_KEY is already set in .env.docker
    if grep -q "^APP_KEY=.*[A-Za-z0-9]" .env.docker; then
        current_key=$(grep "^APP_KEY=" .env.docker | cut -d '=' -f2)
        if [ "$current_key" != "" ] && [ "$current_key" != "null" ] && [ "$current_key" != "base64:null" ]; then
            echo "✅ Application key already exists"
            return 0
        fi
    fi
    
    # Generate new key inside the app container
    docker compose exec app php artisan key:generate --force
    echo "✅ Application key generated"
}

# Function to run database migrations
run_migrations() {
    echo ""
    echo "📊 Running database migrations..."
    
    # Run migrations
    docker compose exec app php artisan migrate --force
    
    echo "✅ Database migrations completed"
}

# Function to seed the database
seed_database() {
    echo ""
    echo "🌱 Seeding database..."
    
    echo "1. Running default seeder..."
    docker compose exec app php artisan db:seed --class=DatabaseSeeder --force
    
    echo "2. Running admin user seeder..."
    docker compose exec app php artisan db:seed --class=AdminUserSeeder --force
    
    echo "✅ Database seeding completed"
}

# Function to show application status
show_status() {
    echo ""
    echo "📋 Application Status:"
    echo "======================"
    docker compose ps
    
    echo ""
    echo "🌐 Access Information:"
    echo "======================"
    echo "Main Application: http://localhost:8000"
    echo "phpMyAdmin:       http://localhost:8080"
    echo ""
    echo "phpMyAdmin Credentials:"
    echo "  Server:   db"
    echo "  Username: laravel_user"
    echo "  Password: laravel_password"
    echo ""
    echo "✅ Deployment completed successfully!"
    echo ""
    echo "💡 Tips:"
    echo "- Run './deploy-docker.sh' again to redeploy"
    echo "- Run 'docker compose logs app' to view application logs"
    echo "- Run 'docker compose down' to stop all containers"
}

# Function to show help
show_help() {
    echo "Usage: ./deploy-docker.sh [OPTIONS]"
    echo ""
    echo "Options:"
    echo "  --help, -h     Show this help message"
    echo "  --rebuild      Rebuild containers from scratch"
    echo "  --migrate-only Only run migrations (skip seeding)"
    echo "  --seed-only    Only run seeding (skip migrations)"
    echo ""
    echo "Examples:"
    echo "  ./deploy-docker.sh              # Full deployment"
    echo "  ./deploy-docker.sh --rebuild    # Clean rebuild"
    echo "  ./deploy-docker.sh --migrate-only # Run only migrations"
}

# Parse command line arguments
REBUILD=false
MIGRATE_ONLY=false
SEED_ONLY=false

while [[ $# -gt 0 ]]; do
    case $1 in
        --help|-h)
            show_help
            exit 0
            ;;
        --rebuild)
            REBUILD=true
            shift
            ;;
        --migrate-only)
            MIGRATE_ONLY=true
            shift
            ;;
        --seed-only)
            SEED_ONLY=true
            shift
            ;;
        *)
            echo "Unknown option: $1"
            show_help
            exit 1
            ;;
    esac
done

# Main deployment process
main() {
    # Check prerequisites
    check_docker
    
    # Handle rebuild if requested
    if [ "$REBUILD" = true ]; then
        echo "🧹 Cleaning previous deployment..."
        docker compose down -v --remove-orphans 2>/dev/null || true
        docker volume prune -f 2>/dev/null || true
        echo "✅ Clean slate prepared"
    else
        # Check for running containers
        check_running_containers
    fi
    
    # If only migrating or seeding, check if containers are running
    if [ "$MIGRATE_ONLY" = true ] || [ "$SEED_ONLY" = true ]; then
        if [ "$(docker compose ps -q 2>/dev/null | wc -l)" -eq 0 ]; then
            echo "⚠️  Containers are not running. Starting them..."
            build_and_start
            wait_for_database
        else
            echo "✅ Containers are already running"
        fi
    else
        # Normal deployment process
        build_and_start
        wait_for_database
        generate_app_key
    fi
    
    # Run migrations if not seed-only
    if [ "$SEED_ONLY" = false ]; then
        run_migrations
    fi
    
    # Run seeding if not migrate-only
    if [ "$MIGRATE_ONLY" = false ]; then
        seed_database
    fi
    
    # Show final status
    show_status
}

# Run main function
main "$@"