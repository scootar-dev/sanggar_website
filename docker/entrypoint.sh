#!/bin/sh
set -e

# Create storage symlink every startup (it's lost on container restart due to volume mount)
echo "[entrypoint] Creating storage symlink..."
rm -f /var/www/html/public/storage
ln -s /var/www/html/storage/app/public /var/www/html/public/storage
echo "[entrypoint] Storage symlink created."

# Set permissions
chown -h www-data:www-data /var/www/html/public/storage 2>/dev/null || true

# Clear cached views (optional, safe to do on startup)
php /var/www/html/artisan view:clear --quiet 2>/dev/null || true

echo "[entrypoint] Starting PHP-FPM and Nginx..."
php-fpm -D
nginx -g 'daemon off;'
