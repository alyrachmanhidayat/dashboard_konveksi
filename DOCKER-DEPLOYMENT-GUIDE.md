# Dashboard Konveksi - Docker Deployment

This document explains how to deploy the Dashboard Konveksi application using Docker.

## Prerequisites

Before running the deployment script, ensure you have:

1. Docker installed (version 20.10 or later)
2. Docker Compose installed (plugin or standalone)

## Deployment Scripts

### Automated Deployment

Use the `deploy-docker.sh` script for automated deployment:

```bash
# Make the script executable
chmod +x deploy-docker.sh

# Run the deployment
./deploy-docker.sh
```

### Deployment Options

The deployment script supports several options:

```bash
# Full deployment (default)
./deploy-docker.sh

# Clean rebuild from scratch
./deploy-docker.sh --rebuild

# Show help
./deploy-docker.sh --help

# Run only migrations (skip seeding)
./deploy-docker.sh --migrate-only

# Run only seeding (skip migrations)
./deploy-docker.sh --seed-only
```

## What the Deployment Script Does

1. **Environment Check**: Verifies Docker and Docker Compose are installed
2. **Container Management**: Stops any existing containers and starts fresh ones
3. **Build Process**: Builds Docker images from the Dockerfile
4. **Database Initialization**: Waits for MySQL to be ready
5. **Application Key Generation**: Creates a new application key if needed
6. **Database Migration**: Runs all Laravel migrations
7. **Database Seeding**: Populates the database with initial data
8. **Service Startup**: Starts all services (Apache, MySQL, Redis, phpMyAdmin)

## Accessing the Application

After deployment, the application will be available at:

- **Main Application**: http://localhost:8000
- **phpMyAdmin**: http://localhost:8080

### phpMyAdmin Credentials
- Server: `db`
- Username: `laravel_user`
- Password: `laravel_password`

## Managing the Application

### Basic Commands

```bash
# View running containers
docker compose ps

# View application logs
docker compose logs app

# View all logs
docker compose logs

# Stop containers
docker compose down

# Start containers
docker compose up -d

# Restart containers
docker compose restart
```

### Advanced Management

```bash
# Access application container shell
docker exec -it dashboard-konveksi-app bash

# Access MySQL shell
docker exec -it dashboard-konveksi-db mysql -u laravel_user -p dashboard_konveksi

# Run Artisan commands
docker compose exec app php artisan migrate
docker compose exec app php artisan db:seed
```

## Data Persistence

The deployment uses Docker volumes to persist data:

- **Database Data**: Stored in `db_data` volume
- **Redis Data**: Stored in `redis_data` volume
- **Application Storage**: Mounted from local `./storage` directory

To completely reset the application data:

```bash
# Stop and remove containers with volumes
docker compose down -v

# Remove unused volumes
docker volume prune
```

## Troubleshooting

### Common Issues

1. **Port Conflicts**: If ports 8000, 8080, 3306, or 6379 are in use:
   - Edit `docker-compose.yml` to change port mappings
   - Or stop the conflicting services

2. **Permission Issues**: On Linux, you might need to adjust file permissions:
   ```bash
   sudo chown -R $(whoami) storage
   sudo chmod -R 775 storage
   ```

3. **Database Connection Errors**:
   - Check if all containers are running: `docker compose ps`
   - View logs: `docker compose logs db`
   - Restart containers: `docker compose restart`

### Reset Everything

To completely start fresh:

```bash
# Stop and remove all containers, networks, and volumes
docker compose down -v --remove-orphans

# Remove unused volumes
docker volume prune

# Remove unused networks
docker network prune

# Run the deployment script again
./deploy-docker.sh
```