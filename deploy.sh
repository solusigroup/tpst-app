#!/bin/bash
set -e

echo "🚀 Bismillahirahmanirrrahiim Starting Deployment..."

# Check if maintenance mode already active
if [ -f storage/framework/down ]; then
    echo "⚠️  Application is already in maintenance mode."
else
    echo "🚧 Entering Maintenance Mode..."
    php artisan down --refresh=15 --retry=60 || true
fi

# Pull the latest changes from GIT
echo "📥 Pulling latest changes from Git..."
git pull origin main

# Install/Update Composer dependencies
echo "📦 Installing Composer dependencies..."
export COMPOSER_ALLOW_SUPERUSER=1
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Install/Update NPM dependencies and build assets
echo "🎨 Building assets..."
if [ -f package-lock.json ]; then
    npm ci
else
    npm install
fi
npm run build

# Run database migrations
echo "🗄️  Running database migrations..."
php artisan migrate --force

# Clear and Cache everything
echo "⚡ Optimizing application (Caches: Config, Route, View)..."
php artisan optimize
php artisan view:cache
php artisan event:cache

# Ensure storage link exists
echo "🔗 Creating storage symbolic link..."
php artisan storage:link || true

# Exit Maintenance Mode
echo "✅ Application is live!"
php artisan up

echo "🚀 Alhamdulillah Deployment finished successfully!"
