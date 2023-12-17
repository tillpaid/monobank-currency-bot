FROM php:8.0-fpm
WORKDIR /app

# Install dependencies
RUN apt-get update && apt-get install -y \
    zip unzip \
    openssl libcurl4 \
    git \
    vim
RUN docker-php-ext-install bcmath pdo_mysql

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
