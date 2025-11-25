# Dashboard Konveksi - Deployment Guide

This guide explains how to deploy the Dashboard Konveksi application on any server with Docker and Docker Compose installed.

## Prerequisites

- Docker installed
- Docker Compose installed
- At least 2GB of free disk space

## Quick Deployment

To deploy the application using the pre-built image from Docker Hub:

1. Create a directory for your deployment:
   ```bash
   mkdir dashboard-konveksi-deployment
   cd dashboard-konveksi-deployment
   ```

2. Copy the production docker-compose file:
   ```bash
   # Create docker-compose.yml with the following content:
   ```

   ```yaml
version: '3.8'

services:
  app:
    image: ryanaputra/dashboard-konveksi-prod:latest
    container_name: dashboard-konveksi-app
    ports:
      - "8000:80"
    environment:
      - DB_HOST=db
      - DB_PORT=3306
      - DB_DATABASE=laravel
      - DB_USERNAME=laravel_user
      - DB_PASSWORD=laravel_password
      - REDIS_HOST=redis
      - REDIS_PASSWORD=null
      - APP_ENV=production
      - APP_DEBUG=false
      - APP_KEY=base64:your-app-key-here
    depends_on:
      - db
      - redis
    volumes:
      - ./storage:/var/www/storage
      - ./public:/var/www/public
    restart: unless-stopped

  db:
    image: mysql:8.0
    container_name: dashboard-konveksi-db
    ports:
      - "3306:3306"
    environment:
      - MYSQL_DATABASE=laravel
      - MYSQL_USER=laravel_user
      - MYSQL_PASSWORD=laravel_password
      - MYSQL_ROOT_PASSWORD=root_password
    volumes:
      - db_data:/var/lib/mysql
    restart: unless-stopped

  redis:
    image: redis:7-alpine
    container_name: dashboard-konveksi-redis
    ports:
      - "6379:6379"
    volumes:
      - redis_data:/data
    restart: unless-stopped

  phpmyadmin:
    image: phpmyadmin:5.2.1
    container_name: dashboard-konveksi-phpmyadmin
    ports:
      - "8080:80"
    environment:
      - PMA_HOST=db
      - MYSQL_USER=laravel_user
      - MYSQL_PASSWORD=laravel_password
    depends_on:
      - db
    restart: unless-stopped

volumes:
  db_data:
  redis_data:
   ```

3. Generate an application key:
   ```bash
   # Generate a Laravel app key
   php -r "echo base64_encode(random_bytes(32));"
   # Or use openssl
   openssl rand -base64 32
   ```

4. Replace the `APP_KEY` value in the docker-compose.yml file with your generated key.

5. Run the application:
   ```bash
   docker-compose up -d
   ```

6. Run the database migrations:
   ```bash
   docker-compose exec app php artisan migrate --force
   ```

7. The application will be available at:
   - Main Application: http://your-server-ip:8000
   - phpMyAdmin: http://your-server-ip:8080

## Production Notes

- The docker-compose file includes persistence for database and Redis using named volumes
- For production, consider securing your server with a reverse proxy (like Nginx) and SSL certificates
- Change default database credentials
- Set up proper backups for your database volume

## Management Commands

- View logs: `docker-compose logs -f`
- Stop the application: `docker-compose down`
- Restart the application: `docker-compose restart`
- Access the application container: `docker-compose exec app bash`

## Updating the Application

To update to the latest version:

1. Pull the latest image:
   ```bash
   docker-compose pull
   ```

2. Recreate the containers:
   ```bash
   docker-compose up -d
   ```

3. Run any new migrations:
   ```bash
   docker-compose exec app php artisan migrate
   ```