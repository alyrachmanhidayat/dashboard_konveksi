# Dashboard Konveksi Production

## Overview
Dashboard Konveksi is a web-based application built with Laravel framework for managing garment manufacturing orders. It's designed to handle the entire workflow from order creation through completion and billing, with features for tracking production progress, managing invoices, and reporting.

## Features

### Core Functionality
- **Dashboard**: Visual overview of all orders with progress tracking and deadline indicators
- **SPK Management (Surat Perintah Kerja)**: Create, edit, and track work orders
- **Progress Tracking**: Monitor orders through cutting, printing, pressing, stitching and delivery stages
- **Invoice Management**: Generate and manage invoices from closed orders
- **Accounts Receivable**: Track and manage outstanding payments
- **Reporting**: Comprehensive reports for completed orders (Omzet) and rejected orders

### Technical Features
- Responsive design using Tailwind CSS
- Database-level locking to prevent race conditions
- File upload support with type and size validation
- Role-based access control
- Secure authentication system

## Installation

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js and npm
- MySQL or compatible database

### Setup Instructions
1. Clone the repository:
   ```bash
   git clone https://github.com/alyrachmanhidayat/dashboard_konveksi-production.git
   cd dashboard_konveksi-production
   ```

2. Install PHP dependencies:
   ```bash
   composer install
   ```

3. Install Node.js dependencies:
   ```bash
   npm install
   ```

4. Create and configure the environment file:
   ```bash
   cp .env.example .env
   ```

5. Generate application key:
   ```bash
   php artisan key:generate
   ```

6. Configure your database in the `.env` file (DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD)

7. Run database migrations:
   ```bash
   php artisan migrate
   ```

8. Build front-end assets:
   ```bash
   npm run build
   ```

9. Start the development server:
   ```bash
   php artisan serve
   ```

## Development

### Running in Development Mode
```bash
php artisan serve
```

For hot-reloading of front-end assets:
```bash
npm run dev
```

### Key Commands
- Run tests: `php artisan test`
- Run database migrations: `php artisan migrate`
- Seed database: `php artisan db:seed`
- Generate application key: `php artisan key:generate`
- Clear caches: `php artisan cache:clear`

### Project Structure
```
dashboard-konveksi/
├── app/                    # Application logic
│   ├── Http/              # Controllers, middleware
│   ├── Models/            # Eloquent models
├── database/              # Migrations, seeds, factories
├── public/                # Public assets
├── resources/             # Views, CSS, JS
├── routes/                # Route definitions
├── storage/               # File storage
├── tests/                 # Test files
```

## Architecture

### Main Components
- **DashboardController**: Manages the main dashboard view and statistics
- **SpkController**: Handles work order creation and management
- **InvoiceController**: Manages invoicing and payment processing
- **RekapController**: Generates reports (requires admin privileges)

### Models
- `Spk`: Main model for work orders
- `SpkSize`: Size-specific quantities for orders
- `Invoice`: Invoice management
- `Payment`: Payment tracking
- `User`: Authentication and authorization

## Security Features
- Role-based access control (admin restrictions)
- Input validation and sanitization
- Secure file upload handling
- Protected routes for sensitive operations

## Contributing
1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Make your changes
4. Commit your changes (`git commit -m 'Add amazing feature'`)
5. Push to the branch (`git push origin feature/amazing-feature`)
6. Open a Pull Request

## Docker Deployment

This project includes an automated Docker deployment script that supports both development and production modes.

### Prerequisites
- Docker and Docker Compose (v2.0 or higher)
- Git
- At least 4GB of RAM available for Docker

### Setup Instructions

1. Clone the repository:
   ```bash
   git clone https://github.com/enzo-nc/dashboard_konveksi-docker.git
   cd dashboard_konveksi-docker
   ```

2. Copy the environment file:
   ```bash
   cp .env.example .env.docker
   ```
   
3. Configure your environment variables in `.env.docker` if needed (database credentials, application key, etc.)

### Usage

The deployment script provides several options:

#### Development Mode (Live Reloading)
For development with live code changes:
```bash
./deploy-docker.sh --rebuild-dev
```
- Code changes on the host are reflected immediately in the container
- No need to rebuild the image when making changes
- Uses volume mounting for the application files

#### Production Mode (Optimized Build)
For production deployment:
```bash
./deploy-docker.sh --rebuild-prod
```
- Files are copied into the Docker image during build
- Dependencies are optimized for production
- Frontend assets are pre-built

#### Other Options
```bash
# Run for first-time deployment
./deploy-docker.sh

# Run migrations only
./deploy-docker.sh --migrate-only

# Run seeding only
./deploy-docker.sh --seed-only

# Show help
./deploy-docker.sh --help
```

### Accessing the Application
After deployment:
- Main Application: http://localhost:8000
- phpMyAdmin: http://localhost:8080
- Default phpMyAdmin credentials:
  - Server: db
  - Username: laravel_user
  - Password: laravel_password

### Docker Services
The setup includes:
- Laravel application (Apache + PHP)
- MySQL database (version 8.0)
- Redis (version 7-alpine) 
- phpMyAdmin (version 5.2.1)

### Stopping Containers
To stop all containers:
```bash
docker compose down  # For production mode
docker compose -f docker-compose.dev.yml down  # For development mode
```

### Deployment

For production deployment without Docker:
1. Set `APP_ENV=production` in your `.env` file
2. Run `php artisan config:cache` to cache configuration
3. Run `php artisan route:cache` to cache routes
4. Run `npm run build` to build production assets
5. Set proper file permissions for storage and bootstrap/cache directories

## License
This project is open source and available under the [MIT License](LICENSE).

## Support
If you have questions, issues, or suggestions, please open an issue in the GitHub repository.