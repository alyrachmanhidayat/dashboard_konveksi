#!/bin/bash

# deploy-docker.sh - Automated deployment script for Dashboard Konveksi
# This script handles first-time setup including database migrations, seeding, and key generation

set -e  # Exit immediately if a command exits with a non-zero status

echo "==============================================="
echo " Dashboard Konveksi - Docker Deployment Script "
echo "==============================================="
echo ""

# Set default to production mode
PROD_MODE=true
DEV_MODE=false

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
    if [ "${DEV_MODE}" = true ]; then
        if [ "$(docker compose -f docker-compose.dev.yml ps -q 2>/dev/null | wc -l)" -gt 0 ]; then
            echo "⚠️  Existing development containers detected. Stopping them..."
            docker compose -f docker-compose.dev.yml down
            echo "✅ Previous development containers stopped"
        fi
    else
        if [ "$(docker compose ps -q 2>/dev/null | wc -l)" -gt 0 ]; then
            echo "⚠️  Existing production containers detected. Stopping them..."
            docker compose down
            echo "✅ Previous production containers stopped"
        fi
    fi
}

# Function to build and start containers
build_and_start() {
    echo ""
    if [ "${DEV_MODE}" = true ]; then
        echo "🐳 Building and starting Docker development containers..."
        echo ""
        # Build and start development containers
        docker compose -f docker-compose.dev.yml up -d --build
    else
        echo "🐳 Building and starting Docker production containers..."
        echo ""
        # Build and start production containers
        docker compose up -d --build
    fi
    
    # Wait for containers to be healthy
    echo "⏳ Waiting for containers to start..."
    sleep 10
    
    # Check if containers are running
    if [ "${DEV_MODE}" = true ]; then
        if [ "$(docker compose -f docker-compose.dev.yml ps -q | wc -l)" -eq 4 ]; then
            echo "✅ All development containers started successfully"
        else
            echo "⚠️  Some development containers may not have started correctly"
            docker compose -f docker-compose.dev.yml ps
        fi
    else
        if [ "$(docker compose ps -q | wc -l)" -eq 4 ]; then
            echo "✅ All production containers started successfully"
        else
            echo "⚠️  Some production containers may not have started correctly"
            docker compose ps
        fi
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
    if [ "${DEV_MODE}" = true ]; then
        docker compose -f docker-compose.dev.yml exec app php artisan key:generate --force
    else
        docker compose exec app php artisan key:generate --force
    fi
    echo "✅ Application key generated"
}

# Function to run database migrations
run_migrations() {
    echo ""
    echo "📊 Running database migrations..."
    
    # Run migrations
    if [ "${DEV_MODE}" = true ]; then
        docker compose -f docker-compose.dev.yml exec app bash -c "php artisan migrate --force 2>&1 | sed -E 's/SQLSTATE\[42S01\].*already exists.*/(Ignored: Table already exists)/' | sed -E 's/SQLSTATE\[42S01\].*Base table or view already exists.*/(Ignored: Table already exists)/' || echo 'Migrations completed (some may have been skipped due to existing tables)'"
    else
        docker compose exec app php artisan migrate --force
    fi
    
    echo "✅ Database migrations completed"
}

# Function to seed the database
seed_database() {
    echo ""
    echo "🌱 Seeding database..."
    
    echo "1. Running default seeder..."
    if [ "${DEV_MODE}" = true ]; then
        docker compose -f docker-compose.dev.yml exec app php artisan db:seed --class=DatabaseSeeder --force
    else
        docker compose exec app php artisan db:seed --class=DatabaseSeeder --force
    fi
    
    echo "2. Running admin user seeder..."
    if [ "${DEV_MODE}" = true ]; then
        docker compose -f docker-compose.dev.yml exec app php artisan db:seed --class=AdminUserSeeder --force
    else
        docker compose exec app php artisan db:seed --class=AdminUserSeeder --force
    fi
    
    echo "✅ Database seeding completed"
}

# Function to show application status
show_status() {
    echo ""
    echo "📋 Application Status:"
    echo "======================"
    if [ "${DEV_MODE}" = true ]; then
        docker compose -f docker-compose.dev.yml ps
    else
        docker compose ps
    fi
    
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
    if [ "${DEV_MODE}" = true ]; then
        echo "💡 Development Tips:"
        echo "- Your code changes will be reflected immediately in the container"
        echo "- No need to rebuild the container when making code changes"
        echo "- Run './deploy-docker.sh --rebuild-dev' to clean rebuild development containers"
        echo ""
    fi
    echo "💡 General Tips:"
    echo "- Run './deploy-docker.sh' again to redeploy"
    echo "- Run 'docker compose logs app' to view application logs"
    echo "- Run 'docker compose down' (or 'docker compose -f docker-compose.dev.yml down' for dev) to stop all containers"
}

# Function to show help
show_help() {
    echo "Usage: ./deploy-docker.sh [OPTIONS]"
    echo ""
    echo "Options:"
    echo "  --help, -h        Show this help message"
    echo "  --rebuild-prod    Rebuild production containers from scratch (copies files into image)"
    echo "  --rebuild-dev     Rebuild development containers with volume mounting (for live code changes)"
    echo "  --migrate-only    Only run migrations (skip seeding)"
    echo "  --seed-only       Only run seeding (skip migrations)"
    echo ""
    echo "Examples:"
    echo "  ./deploy-docker.sh                # Full deployment"
    echo "  ./deploy-docker.sh --rebuild-prod # Production rebuild"
    echo "  ./deploy-docker.sh --rebuild-dev  # Development rebuild with live reload"
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
        --rebuild-prod)
            PROD_MODE=true
            DEV_MODE=false
            REBUILD=true
            shift
            ;;
        --rebuild-dev)
            PROD_MODE=false
            DEV_MODE=true
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
        if [ "${DEV_MODE}" = true ]; then
            docker compose -f docker-compose.dev.yml down -v --remove-orphans 2>/dev/null || true
            docker volume prune -f 2>/dev/null || true
        else
            docker compose down -v --remove-orphans 2>/dev/null || true
            docker volume prune -f 2>/dev/null || true
        fi
        echo "✅ Clean slate prepared"
    else
        # Check for running containers
        check_running_containers
    fi
    
    # If only migrating or seeding, check if containers are running
    if [ "$MIGRATE_ONLY" = true ] || [ "$SEED_ONLY" = true ]; then
        if [ "${DEV_MODE}" = true ]; then
            if [ "$(docker compose -f docker-compose.dev.yml ps -q 2>/dev/null | wc -l)" -eq 0 ]; then
                echo "⚠️  Development containers are not running. Starting them..."
                build_and_start
                wait_for_database
            else
                echo "✅ Development containers are already running"
            fi
        else
            if [ "$(docker compose ps -q 2>/dev/null | wc -l)" -eq 0 ]; then
                echo "⚠️  Production containers are not running. Starting them..."
                build_and_start
                wait_for_database
            else
                echo "✅ Production containers are already running"
            fi
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