FROM php:8.4-cli

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
RUN docker-php-ext-install bcmath zip pcntl posix pdo_sqlite pdo_pgsql

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy application files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Install Node.js for frontend assets
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && apt-get install -y nodejs
RUN npm install && npm run build

# Expose ports
EXPOSE 10000
EXPOSE 8080

# Create supervisord config
RUN mkdir -p /etc/supervisor/conf.d

# Start script with supervisord
COPY --chmod=755 <<'EOF' /usr/local/bin/start.sh
#!/bin/sh

echo "Starting deployment setup..."

# Generate .env file
cat << ENV_EOF > /var/www/.env
APP_NAME="AnonymousChat"
APP_ENV=production
APP_KEY="${APP_KEY}"
APP_DEBUG=false
APP_URL="${RENDER_EXTERNAL_URL:-https://anonchat-90ql.onrender.com}"

DB_CONNECTION=pgsql
DB_URL="${DB_URL}"
DB_FOREIGN_KEYS=true

SESSION_DRIVER=file
CACHE_STORE=file

BROADCAST_CONNECTION=reverb
REVERB_APP_ID="${REVERB_APP_ID}"
REVERB_APP_KEY="${REVERB_APP_KEY}"
REVERB_APP_SECRET="${REVERB_APP_SECRET}"
REVERB_HOST="${REVERB_HOST}"
REVERB_PORT="${REVERB_SERVER_PORT:-8080}"
REVERB_SCHEME="${REVERB_SCHEME:-https}"
REVERB_SERVER_HOST="0.0.0.0"
REVERB_SERVER_PORT="${REVERB_SERVER_PORT:-8080}"
ENV_EOF

echo ".env file generated."

echo "Starting services with supervisord..."

# Start supervisord
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
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
stdout_logfile=/var/log/supervisor/reverb.log
stderr_logfile=/var/log/supervisor/reverb_error.log

[program:laravel]
command=php artisan serve --host=0.0.0.0 --port=10000
directory=/var/www
autostart=true
autorestart=true
stdout_logfile=/var/log/supervisor/laravel.log
stderr_logfile=/var/log/supervisor/laravel_error.log
EOF

CMD ["/usr/local/bin/start.sh"]