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
CMD ["php", "-S", "0.0.0.0:8080", "-t", "public"]
