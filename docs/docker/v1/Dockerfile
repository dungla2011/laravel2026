FROM php:8.3-apache

# Cập nhật hệ thống và cài đặt các gói cần thiết
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libonig-dev \
    unzip \
    git \
    curl \
    libssl-dev \
    pkg-config \
    gnupg

# Cài đặt các extension PHP cần cho Laravel
RUN docker-php-ext-install \
    pdo_mysql \
    mysqli \
    zip \
    gd \
    mbstring \
    bcmath

# Cài đặt MongoDB extension cho PHP qua PECL
RUN pecl install mongodb && docker-php-ext-enable mongodb

# Kích hoạt module Apache rewrite
RUN a2enmod rewrite

# Cài đặt Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Tạo VirtualHost cho Laravel
RUN printf '<VirtualHost *:80>\n\
    DocumentRoot /var/www/html/public\n\
    <Directory /var/www/html/public>\n\
        Options Indexes FollowSymLinks\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
    ErrorLog /var/log/apache2/error.log\n\
    CustomLog /var/log/apache2/access.log combined\n\
</VirtualHost>\n' > /etc/apache2/sites-available/000-default.conf

# Tạo thư mục log Apache nếu chưa có
RUN mkdir -p /var/log/apache2

# Tạo thư mục storage và bootstrap/cache nếu chưa có
RUN mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
	&& mkdir -p /var/glx && chown -R www-data:www-data /var/glx
