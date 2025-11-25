# Docker Setup for Dashboard Konveksi

This project is configured to run in Docker containers with a multi-container setup including:
- Laravel application container (Apache + PHP 8.3.25)
- MySQL database container (MySQL 8.0)
- phpMyAdmin container for database management
- Redis container for caching and sessions

## Prerequisites

- Docker Desktop for Windows

## Automated Deployment (Recommended)

For the easiest deployment, use the automated deployment script:

```bash
# Make the script executable
chmod +x deploy-docker.sh

# Run the automated deployment
./deploy-docker.sh
```

This script will automatically:
- Check Docker installation
- Build and start all containers
- Wait for database readiness
- Generate application key
- Run database migrations
- Seed the database with initial data
- Display access information

See [DOCKER-DEPLOYMENT-GUIDE.md](DOCKER-DEPLOYMENT-GUIDE.md) for detailed deployment options.

## Manual Setup Instructions

If you prefer to set up manually:

1. **Build and start the containers**:
   ```bash
   docker compose up -d
   ```

2. **Wait for services to start**:
   ```bash
   # Wait about 30 seconds for services to initialize
   sleep 30
   ```

3. **Generate application key**:
   ```bash
   docker compose exec app php artisan key:generate --force
   ```

4. **Run database migrations**:
   ```bash
   docker compose exec app php artisan migrate --force
   ```

5. **Seed the database (optional)**:
   ```bash
   docker compose exec app php artisan db:seed --force
   ```

6. **Access the application**:
   - Application: http://localhost:8000
   - phpMyAdmin: http://localhost:8080

## Access Information

After deployment, the application will be available at:

- **Main Application**: http://localhost:8000
- **phpMyAdmin**: http://localhost:8080

### phpMyAdmin Credentials
- Server: `db`
- Username: `laravel_user`
- Password: `laravel_password`

## Managing Containers

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

### Accessing Containers

```bash
# Access the application container shell
docker exec -it dashboard-konveksi-app bash

# Access the database container shell
docker exec -it dashboard-konveksi-db mysql -u laravel_user -p

# Run Artisan commands
docker compose exec app php artisan migrate
docker compose exec app php artisan db:seed
```

## Important Notes

- The `storage` directory is mounted to persist file uploads and cached data
- The `public` directory is mounted to persist publicly accessible files
- The database data is persisted in a Docker volume (`db_data`)
- Redis data is persisted in a Docker volume (`redis_data`)
- MySQL root password is `root_password` (change this in production)
- Database credentials are configured in `docker-compose.yml`
- Port mapping: application runs on port 8000 (host) to port 80 (container)

## Environment Variables

The application uses the following environment variables (configured in `docker-compose.yml`):
- `DB_HOST`: db (container name)
- `DB_PORT`: 3306
- `DB_DATABASE`: dashboard_konveksi
- `DB_USERNAME`: laravel_user
- `DB_PASSWORD`: laravel_password
- `REDIS_HOST`: redis (container name)

## Troubleshooting

If you encounter issues:
1. Check if all containers are running: `docker compose ps`
2. Check container logs: `docker compose logs [service-name]`
3. Make sure ports 8000, 3306, and 8080 are not in use on your host machine

To rebuild the containers:
```bash
docker compose down
docker compose build --no-cache
docker compose up -d
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