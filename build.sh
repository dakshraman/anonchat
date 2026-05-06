#!/bin/bash
set -e

echo "Installing dependencies..."
composer install --no-dev --optimize-autoloader

echo "Generating application key..."
php artisan key:generate --force

echo "Running migrations..."
php artisan migrate --force

echo "Building assets..."
npm install
npm run build

echo "Optimizing Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Deployment ready!"