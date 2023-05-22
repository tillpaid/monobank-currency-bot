FROM php:8.0
WORKDIR /app

EXPOSE ${APPLICATION_PORT}

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
RUN useradd -ms /bin/bash app
RUN chown app:app /app

# Copy docker files
# COPY docker/certs /certs
# COPY docker/php/config.ini /usr/local/etc/php/conf.d/config.ini

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy src
# COPY --chown=app:app . /app

# Install dependencies
# RUN su - app -c "cd /app && composer install"

COPY docker/files/bashrc_part.txt /home/app/bashrc_part.txt
RUN cat /home/app/bashrc_part.txt >> /home/app/.bashrc
