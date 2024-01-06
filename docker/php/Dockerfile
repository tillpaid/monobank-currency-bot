FROM php:8.2-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    zip unzip \
    openssl libcurl4 \
    git \
    vim \
    npm

# Install additional PHP extensions
RUN docker-php-ext-install bcmath pdo_mysql

# Install Xdebug
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# Install nodemon
RUN npm install -g nodemon

# Create user
RUN useradd -ms /bin/bash app

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy bashrc
USER app
COPY docker/php/bashrc_part.txt /home/app/bashrc_part.txt
RUN cat /home/app/bashrc_part.txt >> /home/app/.bashrc

# Finish
EXPOSE 9000
CMD ["php-fpm"]
