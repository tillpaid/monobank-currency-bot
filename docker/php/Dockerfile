FROM php:8.0-fpm
WORKDIR /app

#EXPOSE ${APPLICATION_PORT}

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
RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www

#RUN addgroup --gid $GROUP_ID user
#RUN adduser --disabled-password --gecos '' --uid $USER_ID -g user user
#USER user

#RUN addgroup -g ${GROUP_ID} --system php-user
#RUN adduser -G php-user --system -D -s /bin/bash -u ${USER_ID} php-user

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

EXPOSE 9000
CMD ["php-fpm"]
