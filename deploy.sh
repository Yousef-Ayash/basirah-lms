#!/bin/bash
# deploy.sh
# Usage: ./deploy.sh

# --- Configuration ---
BRANCH="main"
# ---------------------

set -euo pipefail

echo "--- STARTING LARAVEL DEPLOYMENT ---"

# 0. Pull latest code
echo "=> 0. Pulling latest code from origin/$BRANCH..."
# git fetch origin "$BRANCH"
# git reset --hard "origin/$BRANCH"
git pull origin "$BRANCH"

# 1. Install/update PHP dependencies (production safe)
echo ""
echo "=> 1. Installing PHP dependencies (composer install, no-dev, optimized, no-scripts)..."

# Use install (not update) in deployment, and prevent composer scripts from running
composer install --no-dev --optimize-autoloader --no-scripts

# 2. Rebuild frontend assets
echo ""
echo "=> 2. Rebuilding frontend assets (npm run build)..."
npm ci --silent
npm run build:ssr

# 3. Defensive cleanup: remove duplicate migration files (keep oldest)
# echo ""
# echo "=> 3. Cleaning duplicate migrations (if any) — keeping oldest copy for each slug..."

# MIGRATIONS_DIR="database/migrations"

# # Safety: make sure directory exists
# if [ -d "$MIGRATIONS_DIR" ]; then
#   # Iterate over migration files sorted by name ascending (older timestamps first)
#   declare -A SEEN
#   while IFS= read -r -d '' file; do
#     base="$(basename "$file")"
#     # Remove the leading timestamp and underscores to get the slug:
#     # e.g. 2025_10_17_123456_create_personal_access_tokens_table.php -> create_personal_access_tokens_table.php
#     slug="$(printf '%s' "$base" | sed -E 's/^[0-9_]+_//')"

#     if [ -z "${SEEN[$slug]:-}" ]; then
#       SEEN[$slug]="$file"
#     else
#       # Duplicate detected — delete the newer file (we already encountered older one because of sort)
#       echo "    [dup-migration] deleting duplicate: $file  (keeping ${SEEN[$slug]##*/})"
#       rm -f "$file"
#     fi
#   done < <(find "$MIGRATIONS_DIR" -maxdepth 1 -name "*.php" -print0 | sort -z)
# else
#   echo "    migrations dir not found: $MIGRATIONS_DIR (skipping cleanup)"
# fi

# 4. Optional: check migration status (good for debugging)
# echo ""
# echo "=> 4. Checking migration status (not running migrations yet)..."
# php artisan migrate:status || true

# 5. Run Database Migrations (force in production)
echo ""
echo "=> 3. Running database migrations (php artisan migrate --force)..."
php artisan migrate --force


echo ""
echo "=> 4. Generating sitemap.xml file for indexing search crawl"
php artisan sitemap:generate

# 6. Clear and Optimize Caches
echo ""
echo "=> 5. Clearing and optimizing caches..."
php artisan optimize:clear

echo "=> 6. Restarting SSR process..."
# This command tells the running node process to shut down. 
# Supervisor will notice it stopped and automatically restart it with the new code.
php artisan inertia:stop-ssr

echo ""
echo "--- DEPLOYMENT COMPLETE SUCCESSFULLY! ---"

# echo ""
# echo "
# # =================================================================
# # =================================================================
# # DO NOT FORGET TO RUN THIS AFTER FINISHING IF PM2 DID NOT DO IT:
# # ``
# # php artisan inertia:start-ssr
# # ``
# # Note: this runs ssr correctly, ''npm build:ssr'' does not
# # work without running this command
# # =================================================================
# # =================================================================
# "
