#!/bin/bash

# Install PHP dependencies if not already installed
if [ ! -d "vendor" ]; then
  echo "📦 Installing PHP dependencies..."
  composer install --no-interaction --no-progress
  echo "✅ PHP dependencies installed"
else
  echo "📦 PHP dependencies already installed"
  # Check if composer.lock has been modified more recently than vendor directory
  if [ -f "composer.lock" ] && [ composer.lock -nt vendor ]; then
    echo "📦 Updating PHP dependencies based on composer.lock changes..."
    composer install --no-interaction --no-progress
    echo "✅ PHP dependencies updated"
  fi
fi

# Install Node.js dependencies if node_modules doesn't exist
if [ ! -d "node_modules" ]; then
  echo "📦 Installing Node.js dependencies..."
  npm install
  echo "✅ Node.js dependencies installed"
else
  echo "📦 Node.js dependencies already installed"
  # Check if package-lock.json has been modified more recently than node_modules directory
  if [ -f "package-lock.json" ] && [ package-lock.json -nt node_modules ]; then
    echo "📦 Updating Node.js dependencies based on package-lock.json changes..."
    npm install
    echo "✅ Node.js dependencies updated"
  fi
fi

# Wait for the database to be ready
echo "Waiting for database to be ready..."
while ! mysql -h"$DB_HOST" -u"$DB_USERNAME" -p"$DB_PASSWORD" --skip-ssl -e "SELECT 1;" 2>/dev/null; do
  echo "Waiting for database connection..."
  sleep 2
done

echo "Database is ready!"

# Clear any cached configuration to ensure fresh environment variables are loaded
php artisan config:clear 2>/dev/null || echo "Config cache clear not needed or failed"

# Generate application key if not set in the environment file
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "null" ]; then
  echo "Generating application key..."
  php artisan key:generate --force
else
  echo "Application key already set"
fi

# Run migrations, ignoring existing table errors (common when database already has tables)
echo "Running migrations, ignoring pre-existing table errors..."
# Use --force to bypass the check for production environments
php artisan migrate --force 2>&1 | sed -E 's/SQLSTATE\[42S01\].*already exists.*/(Ignored: Table already exists)/' | sed -E 's/SQLSTATE\[42S01\].*Base table or view already exists.*/(Ignored: Table already exists)/' || echo "Migrations completed (some may have been skipped due to existing tables)"

# Clear cache again after migrations
php artisan config:clear 2>/dev/null || echo "Config cache clear not needed or failed"
php artisan cache:clear 2>/dev/null || echo "Cache clear not needed or failed"

# Check if seeding has already been performed by looking for a marker file
if [ ! -f "/var/www/storage/app/seeding_complete" ]; then
  echo "Seeding database for the first time..."
  php artisan db:seed --force
  # Create marker file to indicate seeding has been completed
  mkdir -p /var/www/storage/app
  touch /var/www/storage/app/seeding_complete
  # Ensure proper permissions for the marker file
  chown www-data:www-data /var/www/storage/app/seeding_complete
  echo "Database seeding completed and marker file created."
else
  echo "Database seeding has already been performed. Skipping seeders."
fi

# Start Apache in foreground
echo "Starting Apache server..."
apache2-foreground