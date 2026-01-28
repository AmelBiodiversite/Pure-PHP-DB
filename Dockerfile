# Image PHP avec PostgreSQL
FROM php:8.2-cli

# Installer uniquement PostgreSQL
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql \
    && apt-get clean

# Copier le code
WORKDIR /app
COPY . /app

# Créer les dossiers nécessaires
RUN mkdir -p public/uploads/products public/uploads/avatars public/uploads/shop-logos tmp/sessions \
    && chmod -R 777 public/uploads tmp/sessions

# Port
EXPOSE 8080

# Démarrer le serveur PHP intégré
CMD ["php", "-S", "0.0.0.0:8080", "-t", "public"]FROM php:8.2-apache

# Installer les extensions PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql

# Activer mod_rewrite Apache
RUN a2enmod rewrite

# Copier le code
COPY . /var/www/html/

# Permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Créer les dossiers uploads avec permissions
RUN mkdir -p /var/www/html/public/uploads/products \
    /var/www/html/public/uploads/avatars \
    /var/www/html/public/uploads/shop-logos \
    /var/www/html/tmp/sessions \
    && chmod -R 777 /var/www/html/public/uploads \
    && chmod -R 777 /var/www/html/tmp/sessions

# Port
EXPOSE 80

# Démarrage Apache
CMD ["apache2-foreground"]
