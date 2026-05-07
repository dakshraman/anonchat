FROM dunglas/frankenphp:1.4-php8.4

# Set working directory
WORKDIR /var/www

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libzip-dev \
    unzip \
    libsqlite3-dev \
    libpq-dev \
    supervisor \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN install-php-extensions bcmath zip pcntl posix pdo_sqlite pdo_pgsql intl gd

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy application files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Install Node.js for frontend assets
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && apt-get install -y nodejs
RUN npm install && npm run build

# Set permissions
RUN chown -R www-data:www-data storage bootstrap/cache

# Fix executable permissions
RUN chmod 755 /usr/local/bin/frankenphp

# Create wrapper script for frankenphp
RUN echo '#!/bin/sh\n\
exec /bin/sh -c "exec /usr/local/bin/frankenphp \"\$@\""' > /usr/local/bin/frankenphp-wrapper && \
chmod +x /usr/local/bin/frankenphp-wrapper

# Expose ports
EXPOSE 8080
EXPOSE 10000

# Create supervisord config
RUN mkdir -p /etc/supervisor/conf.d

# Start script
COPY --chmod=755 <<'EOF' /usr/local/bin/start.sh
#!/bin/sh

echo "Starting deployment setup..."

# Generate .env file
cat << ENV_EOF > /var/www/.env
APP_NAME="AnonymousChat"
APP_ENV=production
APP_KEY="base64:MVOoD4Ph1QSoHq1zlcnSzQ6NFpyWCDmzRJxMepNZLBE="
APP_DEBUG=true
APP_URL="${RENDER_EXTERNAL_URL}"

DB_CONNECTION=pgsql
DB_URL="postgresql://anonchatdb:GtGrLgrnHD44OOqZkOiOwJyyyBnBQqJ1@dpg-d7tnqo1j2pic73bfbhh0-a/anonchatdb"
DB_FOREIGN_KEYS=true

SESSION_DRIVER=cookie
CACHE_STORE=file

BROADCAST_CONNECTION=reverb
REVERB_APP_ID="${REVERB_APP_ID}"
REVERB_APP_KEY="${REVERB_APP_KEY}"
REVERB_APP_SECRET="${REVERB_APP_SECRET}"
REVERB_HOST="${REVERB_HOST}"
REVERB_PORT=443
REVERB_SCHEME=https
REVERB_SERVER_HOST="0.0.0.0"
REVERB_SERVER_PORT=8080
ENV_EOF

# Run migrations
php artisan migrate --force

echo "Starting services..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
EOF

# Create custom Caddyfile to reverse proxy Reverb
COPY --chmod=644 <<'EOF' /etc/caddy/Caddyfile
{
    frankenphp
    order php_server before file_server
}

:10000 {
    root * /var/www/public
    
    @reverb {
        path /app/* /apps/*
    }
    reverse_proxy @reverb 127.0.0.1:8080

    php_server
    file_server
}
EOF

# Supervisor config
COPY --chmod=755 <<'EOF' /etc/supervisor/conf.d/supervisord.conf
[supervisord]
nodaemon=true
user=root
logfile=/var/log/supervisor/supervisord.log
pidfile=/var/run/supervisord.pid

[program:reverb]
command=php artisan reverb:start --host=0.0.0.0 --port=8080
directory=/var/www
autostart=true
autorestart=true
stdout_logfile=/dev/stdout
stdout_logfile_maxbytes=0
stderr_logfile=/dev/stderr
stderr_logfile_maxbytes=0

[program:php-app]
command=sh -c "exec php -S 0.0.0.0:10000 -t /var/www/public"
directory=/var/www
autostart=true
autorestart=true
stdout_logfile=/dev/stdout
stdout_logfile_maxbytes=0
stderr_logfile=/dev/stderr
stderr_logfile_maxbytes=0
EOF

ENTRYPOINT ["/usr/local/bin/start.sh"]
