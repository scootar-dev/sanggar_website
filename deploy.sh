#!/bin/bash
# Exit immediately if a command exits with a non-zero status
set -e

# Load global environment paths so non-interactive SSH can find docker, git, etc.
export PATH=$PATH:/usr/local/bin:/usr/bin:/bin:/usr/sbin:/sbin

# Always change to the script's directory
cd "$(dirname "$0")"

echo "🚀 Starting Auto-Deployment Process for Sanggar Mulya Bhakti..."

# 1. Pull the latest code from Git
echo "📥 1. Fetching and resetting to latest code from GitHub..."
git fetch --all
git reset --hard origin/main

# Ensure .env file exists on host so Docker doesn't mount a folder
if [ ! -f .env ]; then
    echo "⚠️ .env not found on server, copying from .env.example..."
    cp .env.example .env
fi

# Ensure storage directories & permissions exist on host before container mounts them
mkdir -p storage/app/public storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
chmod -R 777 storage bootstrap/cache 2>/dev/null || true

# 2. Rebuild Docker Image & Recreate Container
echo "🐳 2. Rebuilding Docker Image and Restarting Container..."

# Safely teardown existing containers to avoid name/network conflicts
if docker compose version >/dev/null 2>&1; then
    echo "Detected: docker compose"
    docker compose down || true
    docker compose up -d --build --remove-orphans
else
    echo "Detected: docker-compose"
    docker-compose down || true
    docker-compose up -d --build --remove-orphans
fi

# 3. Wait for the container to initialize
echo "⏳ 3. Waiting for container to stabilize..."
sleep 5

# 4. Fix storage permissions inside container
echo "🔐 4. Fixing storage permissions..."
docker exec -t sanggar-web chmod -R 777 /var/www/html/storage
docker exec -t sanggar-web chmod -R 777 /var/www/html/bootstrap/cache

# 5. Ensure storage symlink
echo "🔗 5. Ensuring storage symlink..."
docker exec -t sanggar-web php artisan storage:link || true

# 6. Run database migrations
echo "🗄️ 6. Running database migrations inside the container..."
docker exec -t sanggar-web php artisan migrate --force

# 7. Clear and optimize Laravel caches
echo "🧹 7. Optimizing and clearing Laravel caches inside the container..."
docker exec -t sanggar-web php artisan optimize:clear
docker exec -t sanggar-web php artisan config:cache
docker exec -t sanggar-web php artisan route:cache
docker exec -t sanggar-web php artisan view:cache

# 8. Clean dangling images to save disk space
echo "🧼 8. Cleaning dangling images..."
docker image prune -f || true

echo "🎉 Auto-Deployment completed successfully for Sanggar Mulya Bhakti! Container is running on port 8087."
