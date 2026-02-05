# Image PHP avec PostgreSQL et Composer
FROM php:8.3-cli

# Installer PostgreSQL et extension mbstring
RUN apt-get update && apt-get install -y \
    libpq-dev \
    git \
    unzip \
    && docker-php-ext-install pdo pdo_pgsql pgsql mbstring \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Installer Composer depuis l'image officielle
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Définir le répertoire de travail
WORKDIR /app

# Copier les fichiers de dépendances en premier (pour le cache Docker)
COPY composer.json composer.lock ./

# Installer les dépendances PHP (CRITIQUE - c'était manquant !)
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Copier le reste de l'application
COPY . .

# Créer les dossiers nécessaires avec permissions
RUN mkdir -p public/uploads/products public/uploads/avatars public/uploads/shop-logos tmp/sessions \
    && chmod -R 777 public/uploads tmp/sessions

# Exposer le port Railway
EXPOSE 8080

# Démarrer le serveur PHP intégré avec router
CMD ["php", "-S", "0.0.0.0:8080", "-t", "public", "public/router.php"]# Image PHP avec PostgreSQL
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

# Démarrer le serveur PHP intégré avec router
CMD ["php", "-S", "0.0.0.0:8080", "-t", "public", "public/router.php"]
