#!/bin/bash

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
php artisan migrate --force 2>&1 | sed 's/SQLSTATE\[42S01\].*already exists.*/(Ignored: Table already exists)/' || echo "Migrations completed (some may have been skipped due to existing tables)"

# Clear cache again after migrations
php artisan config:clear 2>/dev/null || echo "Config cache clear not needed or failed"
php artisan cache:clear 2>/dev/null || echo "Cache clear not needed or failed"

# If we're in development, seed the database
if [ "$APP_ENV" = "local" ] || [ "$APP_ENV" = "development" ]; then
  echo "Seeding database..."
  php artisan db:seed --force
else
  # For production, run seeders but ignore errors if they already ran
  php artisan db:seed --force 2>&1 | sed 's/SQLSTATE\[23000\].*Integrity constraint violation.*/(Ignored: Possible duplicate entry)/' || echo "Seeding completed or already run"
fi

# Start Apache in foreground
echo "Starting Apache server..."
apache2-foreground