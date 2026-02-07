# ============================================================================
# MARKETFLOW PRO - Dockerfile Production (Apache)
# ============================================================================
FROM php:8.3.0-apache

# Installer les dépendances système et extensions PHP
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libonig-dev \
    libcurl4-openssl-dev \
    git \
    unzip \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_pgsql \
        pgsql \
        mbstring \
        curl \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Activer mod_rewrite pour les URLs propres
RUN a2enmod rewrite

# Fix MPM conflict: disable all except prefork (required for mod_php)
RUN a2dismod mpm_event mpm_worker || true && a2enmod mpm_prefork

# Configurer Apache : DocumentRoot = /app/public, port 8080
RUN sed -i 's|/var/www/html|/app/public|g' /etc/apache2/sites-available/000-default.conf \
    && sed -i 's|/var/www/html|/app/public|g' /etc/apache2/apache2.conf \
    && sed -i 's|Listen 80|Listen 8080|g' /etc/apache2/ports.conf \
    && sed -i 's|:80>|:8080>|g' /etc/apache2/sites-available/000-default.conf

# Autoriser .htaccess (AllowOverride All) + Directory parent pour securite
RUN cat <<EOC >> /etc/apache2/apache2.conf
<Directory /app>
    Options -Indexes +FollowSymLinks
    AllowOverride None
    Require all granted
</Directory>
<Directory /app/public>
    Options -Indexes +FollowSymLinks
    AllowOverride All
    Require all granted
</Directory>
EOC

# Logs verbose pour debug
RUN sed -i 's/LogLevel info/LogLevel warn/' /etc/apache2/apache2.conf

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Installer dépendances PHP
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --prefer-dist \
    --no-scripts

# Copier le code source
COPY . .

# Optimiser autoloader
RUN composer dump-autoload --optimize --no-dev

# Créer les dossiers avec permissions
RUN mkdir -p \
    public/uploads/products \
    public/uploads/avatars \
    public/uploads/shop-logos \
    tmp/sessions \
    data/logs \
    && chmod -R 777 public/uploads tmp/sessions data/logs

# ENV prod (desactive debug en prod)
ENV APP_ENV=production
ENV APP_DEBUG=false

# Chown pour Apache (user www-data)
RUN chown -R www-data:www-data /app

EXPOSE 8080

CMD ["bash", "-c", "set -eux; a2dismod mpm_event mpm_worker || true; rm -f /etc/apache2/mods-enabled/mpm_event.* /etc/apache2/mods-enabled/mpm_worker.* || true; a2enmod mpm_prefork; apache2ctl -t; exec apache2-foreground"]
