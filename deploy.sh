#!/bin/bash

# --- Configuration ---
BRANCH="main"
# ---------------------

# Exit immediately if a command exits with a non-zero status
set -e

echo "--- STARTING LARAVEL DEPLOYMENT ---"

# Pull the latest code from GitHub
echo "=> 0. Pulling latest code from origin/$BRANCH..."
git pull origin "$BRANCH"

# Update PHP Dependencies
echo ""
echo "=> 1. Updating PHP dependencies (composer install)..."
# Use --no-dev and --optimize-autoloader for production
composer update
composer install --no-dev --optimize-autoloader

# Rebuild Frontend Assets (Adjust the command based on your setup: mix or vite)
echo ""
echo "=> 3. Rebuilding frontend assets (npm run build)..."
npm install
npm run build

# Run Database Migrations
echo ""
echo "=> 4. Running database migrations..."
# The --force flag confirms the migration in a production environment
php artisan migrate --force

# Clear and Optimize Caches
echo ""
echo "=> 5. Clearing and optimizing caches..."
# This single command clears view, route, config, and application cache
php artisan optimize:clear

echo ""
echo "--- DEPLOYMENT COMPLETE SUCCESSFULLY! ---"