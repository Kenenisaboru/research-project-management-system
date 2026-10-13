FROM php:8.2-fpm

# ──────────────────────────────────────────────────────────────────────────────
# System dependencies + Nginx + Supervisor (for queue worker)
# ──────────────────────────────────────────────────────────────────────────────
RUN apt-get update && apt-get install -y \
    nginx \
    supervisor \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    sqlite3 \
    libsqlite3-dev \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# ──────────────────────────────────────────────────────────────────────────────
# PHP Extensions (including GD with JPEG + FreeType support)
# ──────────────────────────────────────────────────────────────────────────────
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo_mysql \
        pdo_sqlite \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip

# ──────────────────────────────────────────────────────────────────────────────
# Composer
# ──────────────────────────────────────────────────────────────────────────────
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ──────────────────────────────────────────────────────────────────────────────
# Application Files
# ──────────────────────────────────────────────────────────────────────────────
WORKDIR /var/www
COPY . /var/www

# Nginx config
COPY nginx.conf /etc/nginx/sites-available/default

# Install production dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# ──────────────────────────────────────────────────────────────────────────────
# Supervisor Configuration
# Queue worker runs alongside PHP-FPM when RPMS_ENABLE_QUEUE_WORKER=true
# ──────────────────────────────────────────────────────────────────────────────
COPY <<'EOF' /etc/supervisor/conf.d/rpms.conf
[supervisord]
nodaemon=true
logfile=/var/log/supervisor/supervisord.log
pidfile=/var/run/supervisord.pid

[program:php-fpm]
command=php-fpm --nodaemonize
autostart=true
autorestart=true
stdout_logfile=/dev/stdout
stdout_logfile_maxbytes=0
stderr_logfile=/dev/stderr
stderr_logfile_maxbytes=0

[program:nginx]
command=nginx -g "daemon off;"
autostart=true
autorestart=true
stdout_logfile=/dev/stdout
stdout_logfile_maxbytes=0
stderr_logfile=/dev/stderr
stderr_logfile_maxbytes=0

[program:queue-worker]
command=php /var/www/artisan queue:work --sleep=3 --tries=3 --max-time=3600 --queue=default,notifications
autostart=false
autorestart=true
stdout_logfile=/var/www/storage/logs/worker.log
stderr_logfile=/var/www/storage/logs/worker-error.log
stopasgroup=true
killasgroup=true
EOF

# ──────────────────────────────────────────────────────────────────────────────
# Storage directories and permissions
# ──────────────────────────────────────────────────────────────────────────────
RUN mkdir -p \
    /var/www/storage/logs \
    /var/www/storage/framework/cache \
    /var/www/storage/framework/sessions \
    /var/www/storage/framework/views \
    /var/www/bootstrap/cache \
    /var/log/supervisor \
    /data \
    && chown -R www-data:www-data \
        /var/www/storage \
        /var/www/bootstrap/cache \
        /data

# ──────────────────────────────────────────────────────────────────────────────
# Entrypoint
# ──────────────────────────────────────────────────────────────────────────────
COPY entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80

HEALTHCHECK --interval=30s --timeout=10s --start-period=60s --retries=3 \
    CMD curl -f http://localhost/health || exit 1

ENTRYPOINT ["entrypoint.sh"]
