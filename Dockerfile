FROM php:8.4-cli

WORKDIR /var/www

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libzip-dev \
    unzip \
    libpq-dev \
    libsqlite3-dev \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy application files first
COPY . .

# Create .env from example if not exists
RUN cp -n .env.example .env || true

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Install Node.js for frontend assets
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && apt-get install -y nodejs

# Generate application key
RUN php artisan key:generate

# Create SQLite database if using sqlite
RUN touch /var/www/database/database.sqlite

# Run migrations
RUN php artisan migrate --force

# Set permissions
RUN chmod -R 755 /var/www/bootstrap/cache \
    && chmod -R 755 /var/www/storage \
    && chmod 666 /var/www/database/database.sqlite

# Expose port
EXPOSE 10000

# Start Laravel
CMD ["sh", "-c", "php artisan serve --host=0.0.0.0 --port=10000"]