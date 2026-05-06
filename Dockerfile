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
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install bcmath zip pcntl posix pdo_sqlite

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

# Start script
COPY --chmod=755 <<'EOF' /usr/local/bin/start.sh
#!/bin/sh

echo "Starting deployment setup..."

# 1. Generate the perfect .env file dynamically
cat << ENV_EOF > /var/www/.env
APP_NAME="AnonymousChat"
APP_ENV=production
APP_KEY="base64:7Kscf6uYp8Z5K6m1T440IdIt4poLMP464hNUTCHD5A="
APP_DEBUG=false
APP_URL="${RENDER_EXTERNAL_URL:-https://anonchat-90ql.onrender.com}"

DB_CONNECTION=sqlite
DB_DATABASE=/var/www/database/database.sqlite
DB_FOREIGN_KEYS=true

SESSION_DRIVER=file
CACHE_STORE=file

BROADCAST_CONNECTION=reverb
REVERB_APP_ID=123456
REVERB_APP_KEY=anonchat_key
REVERB_APP_SECRET=anonchat_secret
REVERB_HOST=0.0.0.0
REVERB_PORT=8080
REVERB_SCHEME=https
ENV_EOF

echo ".env file generated successfully."

# 2. Setup SQLite Database
mkdir -p /var/www/database
touch /var/www/database/database.sqlite

# 3. Setup required Laravel storage directories
mkdir -p /var/www/storage/framework/cache/data
mkdir -p /var/www/storage/framework/sessions
mkdir -p /var/www/storage/framework/views
mkdir -p /var/www/storage/logs
mkdir -p /var/www/bootstrap/cache

# 4. Set extremely permissive permissions to avoid any read/write errors
chmod -R 777 /var/www/database
chmod -R 777 /var/www/storage
chmod -R 777 /var/www/bootstrap/cache
chown -R www-data:www-data /var/www

# 5. Clear caches
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 6. Run Migrations
php artisan migrate --force

echo "Setup complete. Starting servers..."

# 7. Start Reverb (background)
php artisan reverb:start --host=0.0.0.0 --port=8080 &

# 8. Start Laravel (foreground)
exec php artisan serve --host=0.0.0.0 --port=${PORT:-10000}
EOF

CMD ["/usr/local/bin/start.sh"]