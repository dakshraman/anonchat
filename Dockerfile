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

# Ensure SQLite database exists and is writable
RUN mkdir -p database && touch database/database.sqlite && chmod 666 database/database.sqlite

# Set permissions
RUN chmod -R 777 /var/www/storage /var/www/bootstrap/cache /var/www/database \
    && chown -R www-data:www-data /var/www

# Expose Reverb port
EXPOSE 8080

# Start script
COPY --chmod=755 <<EOF /usr/local/bin/start.sh
#!/bin/sh
# Clear and cache settings
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Run migrations
php artisan migrate --force

# Start Reverb in the background
php artisan reverb:start --host=0.0.0.0 --port=8080 &

# Start Laravel on the port Render provides
php artisan serve --host=0.0.0.0 --port=\${PORT:-10000}
EOF

CMD ["/usr/local/bin/start.sh"]