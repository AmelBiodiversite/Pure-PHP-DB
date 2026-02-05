# Image PHP 8.3 avec CLI
FROM php:8.3-cli

# Installer les dépendances système et les extensions PHP en une seule couche
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libonig-dev \
    git \
    unzip \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_pgsql \
        pgsql \
        mbstring \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Installer Composer depuis l'image officielle
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Définir le répertoire de travail
WORKDIR /app

# Copier les fichiers de dépendances (optimisation du cache Docker)
COPY composer.json composer.lock ./

# Installer les dépendances PHP sans interaction
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --prefer-dist \
    --no-scripts

# Copier tout le code source
COPY . .

# Exécuter les scripts post-install si nécessaire
RUN composer dump-autoload --optimize --no-dev

# Créer les dossiers avec permissions
RUN mkdir -p \
    public/uploads/products \
    public/uploads/avatars \
    public/uploads/shop-logos \
    tmp/sessions \
    && chmod -R 777 public/uploads tmp/sessions

# Exposer le port
EXPOSE 8080

# Commande de démarrage
CMD ["php", "-S", "0.0.0.0:8080", "-t", "public", "public/router.php"]
