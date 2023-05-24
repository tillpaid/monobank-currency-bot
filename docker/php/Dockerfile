FROM php:8.0-fpm
WORKDIR /app

ARG USER_ID
ARG GROUP_ID

# Install needle packages
RUN apt-get update && apt-get install -y \
    zip \
    unzip \
    openssl \
    libcurl4 \
    git \
    vim
RUN docker-php-ext-install bcmath pdo_mysql

# Create user
RUN groupadd -g $GROUP_ID www
RUN useradd -u $USER_ID -ms /bin/bash -g www www

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

USER www

COPY docker/files/bashrc_part.txt /home/www/bashrc_part.txt
RUN cat /home/www/bashrc_part.txt >> /home/www/.bashrc

EXPOSE 9000
CMD ["php-fpm"]
