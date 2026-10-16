#!/bin/sh
set -eu

# ============================================================================
# RPMS Docker Entrypoint
#
# Steps:
#   1. Fix storage/bootstrap permissions
#   2. Handle SQLite database file (dev/single-container mode)
#   3. Run Laravel migrations and optional seeders
#   4. Queue a restart signal for any running workers
#   5. Optimise Laravel for production (config/route/view cache)
#   6. Start supervisor (manages php-fpm, nginx, optional queue worker)
#      OR fall back to php-fpm + nginx directly
# ============================================================================

# ─────────────────────────────────────────────────────────────────────────────
# 1. Storage & bootstrap permissions
# ─────────────────────────────────────────────────────────────────────────────
mkdir -p /var/www/storage/logs
mkdir -p /var/www/storage/framework/cache /var/www/storage/framework/sessions /var/www/storage/framework/views
mkdir -p /var/www/bootstrap/cache
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# ─────────────────────────────────────────────────────────────────────────────
# 2. SQLite setup (only applies when DB_CONNECTION=sqlite)
# ─────────────────────────────────────────────────────────────────────────────
DB_CONN="${DB_CONNECTION:-sqlite}"
if [ "$DB_CONN" = "sqlite" ]; then
    DB_PATH="${DB_DATABASE:-/var/www/database/database.sqlite}"
    DB_DIR="$(dirname "$DB_PATH")"
    mkdir -p "$DB_DIR"
    chown -R www-data:www-data "$DB_DIR"
    chmod -R 775 "$DB_DIR"
    touch "$DB_PATH"
    chown www-data:www-data "$DB_PATH"
    chmod 664 "$DB_PATH"
fi

# ─────────────────────────────────────────────────────────────────────────────
# 3. Migrations and seeders
# ─────────────────────────────────────────────────────────────────────────────
php /var/www/artisan migrate --force
php /var/www/artisan db:seed --class=RbacSeeder --force

if [ "${RPMS_SEED_DEMO_DATA:-false}" = "true" ] || [ "${RPMS_SHOW_DEMO_ACCOUNTS:-false}" = "true" ]; then
    php /var/www/artisan db:seed --force
fi

# ─────────────────────────────────────────────────────────────────────────────
# 4. Queue restart signal (graceful worker reload)
# ─────────────────────────────────────────────────────────────────────────────
php /var/www/artisan queue:restart || true

# ─────────────────────────────────────────────────────────────────────────────
# 5. Laravel production optimisation
# ─────────────────────────────────────────────────────────────────────────────
php /var/www/artisan config:cache
php /var/www/artisan route:cache
php /var/www/artisan view:cache

# Fix permissions after cache commands
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# ─────────────────────────────────────────────────────────────────────────────
# 6. Start supervisor OR plain php-fpm + nginx
# ─────────────────────────────────────────────────────────────────────────────
if [ "${RPMS_ENABLE_QUEUE_WORKER:-false}" = "true" ]; then
    echo "[entrypoint] Starting supervisord (php-fpm + nginx + queue-worker)..."
    # Enable the queue-worker program in supervisor before starting
    sed -i 's/^autostart=false/autostart=true/' /etc/supervisor/conf.d/rpms.conf
    exec /usr/bin/supervisord -c /etc/supervisor/supervisord.conf
else
    echo "[entrypoint] Starting php-fpm + nginx (no queue worker)..."
    php-fpm -D
    exec nginx -g "daemon off;"
fi
