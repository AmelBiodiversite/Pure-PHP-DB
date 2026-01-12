# 📦 Guide d'Installation MarketFlow Pro

> Installation complète en 30 minutes • Support inclus • Environnement test fourni

---

## 📋 Prérequis Système

### Minimum Requis

- ✅ **PHP** : 8.0 ou supérieur
- ✅ **MySQL** : 5.7 ou supérieur (ou MariaDB 10.2+)
- ✅ **Extensions PHP** :
  - `pdo_mysql`
  - `mbstring`
  - `json`
  - `curl`
  - `gd` (traitement images)
  - `fileinfo`
- ✅ **Serveur Web** : Apache 2.4+ ou Nginx 1.18+
- ✅ **Espace Disque** : 500 MB minimum
- ✅ **RAM** : 512 MB minimum (2GB recommandé)

### Recommandé

- ✅ PHP 8.1+
- ✅ MySQL 8.0+
- ✅ 2 CPU cores
- ✅ 4GB RAM
- ✅ SSD storage

---

## 🚀 Installation Rapide (10 minutes)

### Étape 1 : Télécharger les Fichiers

```bash
# Extraire le ZIP dans votre dossier web
unzip marketflow-pro.zip
cd marketflow-pro
```

### Étape 2 : Créer la Base de Données

```bash
# Se connecter à MySQL
mysql -u root -p

# Créer la base
CREATE DATABASE marketflow_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Créer un utilisateur (optionnel mais recommandé)
CREATE USER 'marketflow_user'@'localhost' IDENTIFIED BY 'MOT_DE_PASSE_FORT';
GRANT ALL PRIVILEGES ON marketflow_db.* TO 'marketflow_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Étape 3 : Importer le Schéma

```bash
mysql -u root -p marketflow_db < database/marketflow.sql
```

### Étape 4 : Configuration

```bash
# Copier le fichier de config
cp config/config.example.php config/config.php

# Éditer avec vos paramètres
nano config/config.php
```

Modifiez ces lignes :

```php
// Base de données
define('DB_HOST', 'localhost');
define('DB_NAME', 'marketflow_db');
define('DB_USER', 'marketflow_user');
define('DB_PASS', 'VOTRE_MOT_DE_PASSE');

// URL de votre site
define('APP_URL', 'https://votre-domaine.com');

// Stripe (mode TEST pour commencer)
define('STRIPE_PUBLIC_KEY', 'pk_test_VOTRE_CLE');
define('STRIPE_SECRET_KEY', 'sk_test_VOTRE_CLE');
```

### Étape 5 : Permissions Fichiers

```bash
# Créer les dossiers uploads
mkdir -p public/uploads/{products/{thumbnails,files,gallery},avatars,shops}

# Permissions (Linux/Mac)
chmod -R 755 public/uploads
chown -R www-data:www-data public/uploads  # Apache/Nginx user

# Vérifier
ls -la public/uploads
```

### Étape 6 : Accéder au Site

```
http://votre-domaine.com
```

✅ **C'est tout ! Votre marketplace est prête !** 🎉

---

## 🔧 Configuration Serveur Web

### Apache

Créez ou éditez `.htaccess` à la racine :

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    
    # Rediriger vers HTTPS (production)
    # RewriteCond %{HTTPS} off
    # RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
    
    # Router tout vers index.php
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php [QSA,L]
</IfModule>

# Protection fichiers sensibles
<FilesMatch "^\.">
    Order allow,deny
    Deny from all
</FilesMatch>

# Désactiver listing dossiers
Options -Indexes

# Limiter taille upload (50MB)
php_value upload_max_filesize 50M
php_value post_max_size 50M
```

### Nginx

Créez `/etc/nginx/sites-available/marketflow` :

```nginx
server {
    listen 80;
    server_name votre-domaine.com;
    root /var/www/marketflow-pro;
    index index.php;

    # Taille max upload
    client_max_body_size 50M;

    # Logs
    access_log /var/log/nginx/marketflow-access.log;
    error_log /var/log/nginx/marketflow-error.log;

    # Routing
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Protection fichiers
    location ~ /\.(?!well-known) {
        deny all;
    }

    # Cache statique
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 30d;
        add_header Cache-Control "public, immutable";
    }

    # Sécurité uploads
    location ^~ /public/uploads/ {
        internal;
    }
}

# SSL (recommandé en production)
# server {
#     listen 443 ssl http2;
#     server_name votre-domaine.com;
#     
#     ssl_certificate /path/to/cert.pem;
#     ssl_certificate_key /path/to/key.pem;
#     
#     # ... reste de la config
# }
```

Activez :

```bash
sudo ln -s /etc/nginx/sites-available/marketflow /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

---

## 🔐 Configuration Stripe

### 1. Créer un Compte

- Allez sur [stripe.com](https://stripe.com)
- Créez un compte gratuit
- Activez les paiements test

### 2. Récupérer les Clés

**Dashboard** → **Développeurs** → **Clés API**

Vous aurez 4 clés :
- `pk_test_xxx` - Clé publique TEST
- `sk_test_xxx` - Clé secrète TEST
- `pk_live_xxx` - Clé publique LIVE
- `sk_live_xxx` - Clé secrète LIVE

### 3. Configurer dans MarketFlow

```php
// config/config.php

// MODE TEST (développement)
define('STRIPE_PUBLIC_KEY', 'pk_test_VOTRE_CLE');
define('STRIPE_SECRET_KEY', 'sk_test_VOTRE_CLE');

// Pour passer en LIVE (production)
// define('STRIPE_PUBLIC_KEY', 'pk_live_VOTRE_CLE');
// define('STRIPE_SECRET_KEY', 'sk_live_VOTRE_CLE');
```

### 4. Configurer les Webhooks

**Dashboard Stripe** → **Développeurs** → **Webhooks** → **Ajouter un endpoint**

URL à configurer :
```
https://votre-domaine.com/webhooks/stripe
```

Événements à écouter :
- `checkout.session.completed`
- `payment_intent.succeeded`
- `payment_intent.payment_failed`
- `charge.refunded`

Copiez le **Secret du webhook** (`whsec_xxx`) :

```php
define('STRIPE_WEBHOOK_SECRET', 'whsec_VOTRE_SECRET');
```

---

## 📧 Configuration Email (Optionnel)

### Gmail SMTP

```php
// config/config.php

define('SMTP_ENABLED', true);
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_SECURE', 'tls');
define('SMTP_USER', 'votre@gmail.com');
define('SMTP_PASS', 'MOT_DE_PASSE_APPLICATION'); // Voir ci-dessous
define('SMTP_FROM', 'noreply@votresite.com');
define('SMTP_FROM_NAME', 'MarketFlow Pro');
```

#### Créer un Mot de Passe d'Application Gmail

1. Allez sur [myaccount.google.com](https://myaccount.google.com)
2. **Sécurité** → **Validation en 2 étapes** (activez-la)
3. **Mots de passe des applications**
4. Créez un mot de passe pour "Autre (nom personnalisé)"
5. Copiez le mot de passe généré (16 caractères)

### SendGrid (Recommandé pour Production)

```php
define('SMTP_HOST', 'smtp.sendgrid.net');
define('SMTP_PORT', 587);
define('SMTP_USER', 'apikey');
define('SMTP_PASS', 'VOTRE_CLE_API_SENDGRID');
```

---

## 👑 Créer un Compte Admin

### Via Interface (Recommandé)

1. Allez sur `/register`
2. Créez un compte avec role "Buyer"
3. Connectez-vous à MySQL :

```sql
UPDATE users 
SET role = 'admin' 
WHERE email = 'votre@email.com';
```

### Via SQL Directement

```sql
INSERT INTO users (
    full_name, 
    username, 
    email, 
    password, 
    role, 
    is_active
) VALUES (
    'Admin Principal',
    'admin',
    'admin@votresite.com',
    '$2y$12$ABC...XYZ',  -- Hash BCrypt de "admin123"
    'admin',
    1
);
```

Pour générer le hash du mot de passe :

```php
<?php
echo password_hash('votre_mot_de_passe', PASSWORD_BCRYPT, ['cost' => 12]);
```

---

## 🧪 Tests de Fonctionnement

### Checklist Complète

```bash
# 1. Page d'accueil charge
curl -I http://votre-domaine.com
# Doit retourner 200 OK

# 2. Login fonctionne
http://votre-domaine.com/login

# 3. Inscription fonctionne
http://votre-domaine.com/register

# 4. Catalogue charge
http://votre-domaine.com/products

# 5. Dashboard admin accessible
http://votre-domaine.com/admin
```

### Test Stripe (Mode Test)

Utilisez ces cartes test :

- **Succès** : `4242 4242 4242 4242`
- **Échec** : `4000 0000 0000 0002`
- **Authentification** : `4000 0027 6000 3184`

Toujours :
- Date expiration : Toute date future
- CVC : N'importe quel 3 chiffres
- Code postal : N'importe lequel

---

## 🐛 Dépannage

### Problème : Page Blanche

```bash
# Activer affichage erreurs
nano config/config.php

# Ajoutez en haut
ini_set('display_errors', 1);
error_reporting(E_ALL);
```

### Problème : 404 sur Toutes les Pages

```bash
# Vérifier mod_rewrite Apache
sudo a2enmod rewrite
sudo systemctl restart apache2

# Vérifier .htaccess existe
ls -la .htaccess

# Vérifier AllowOverride dans Apache config
sudo nano /etc/apache2/sites-available/000-default.conf
# Ajouter : AllowOverride All
```

### Problème : Erreur Connexion BDD

```bash
# Tester connexion MySQL
mysql -u marketflow_user -p marketflow_db

# Vérifier credentials config/config.php
cat config/config.php | grep DB_

# Vérifier que la BDD existe
mysql -u root -p -e "SHOW DATABASES LIKE 'marketflow%';"
```

### Problème : Upload Échoue

```bash
# Vérifier permissions
ls -la public/uploads

# Corriger si besoin
sudo chown -R www-data:www-data public/uploads
sudo chmod -R 755 public/uploads

# Vérifier limites PHP
php -i | grep upload_max_filesize
php -i | grep post_max_size
```

### Problème : Stripe Ne Fonctionne Pas

1. Vérifiez les clés dans `config/config.php`
2. Vérifiez mode TEST vs LIVE
3. Testez avec carte `4242 4242 4242 4242`
4. Consultez les logs Stripe Dashboard

---

## 🚀 Mise en Production

### Checklist Pré-Lancement

- [ ] **Base de données** backupée
- [ ] **Stripe LIVE** configuré
- [ ] **HTTPS** activé (Let's Encrypt)
- [ ] **Emails** fonctionnels
- [ ] **Erreurs** désactivées (`display_errors = 0`)
- [ ] **Permissions** vérifiées
- [ ] **Backups** automatiques configurés
- [ ] **Monitoring** activé (Sentry, etc.)
- [ ] **CDN** configuré (optionnel)
- [ ] **Firewall** activé
- [ ] **Tests** complets effectués

### Configuration Production

```php
// config/config.php

// Environnement
define('ENVIRONMENT', 'production');

// Désactiver erreurs
ini_set('display_errors', 0);
error_reporting(0);

// Stripe LIVE
define('STRIPE_PUBLIC_KEY', 'pk_live_XXX');
define('STRIPE_SECRET_KEY', 'sk_live_XXX');

// Forcer HTTPS
define('FORCE_HTTPS', true);

// Logs
define('ERROR_LOG', '/var/log/marketflow/error.log');
```

### SSL/HTTPS (Let's Encrypt)

```bash
# Installer Certbot
sudo apt install certbot python3-certbot-apache

# Obtenir certificat SSL
sudo certbot --apache -d votre-domaine.com -d www.votre-domaine.com

# Renouvellement auto (cron)
sudo certbot renew --dry-run
```

### Backups Automatiques

```bash
# Créer script backup
sudo nano /usr/local/bin/backup-marketflow.sh
```

```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backups/marketflow"

# Backup BDD
mysqldump -u USER -pPASS marketflow_db > "$BACKUP_DIR/db_$DATE.sql"

# Backup fichiers
tar -czf "$BACKUP_DIR/files_$DATE.tar.gz" /var/www/marketflow-pro/public/uploads

# Garder seulement 30 jours
find $BACKUP_DIR -name "*.sql" -mtime +30 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +30 -delete
```

```bash
# Rendre exécutable
sudo chmod +x /usr/local/bin/backup-marketflow.sh

# Cron quotidien (3h du matin)
sudo crontab -e
# Ajouter : 0 3 * * * /usr/local/bin/backup-marketflow.sh
```

---

## 📊 Monitoring

### Logs à Surveiller

```bash
# Logs Apache
tail -f /var/log/apache2/error.log

# Logs Nginx
tail -f /var/log/nginx/error.log

# Logs PHP
tail -f /var/log/php8.0-fpm.log

# Logs Application
tail -f /var/log/marketflow/error.log
```

### Outils Recommandés

- **Uptime** : UptimeRobot (gratuit)
- **Erreurs** : Sentry
- **Analytics** : Google Analytics
- **Performance** : New Relic / Datadog

---

## 💡 Optimisations Performance

### PHP OPcache

```bash
# Activer OPcache
sudo nano /etc/php/8.0/apache2/php.ini
```

```ini
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=10000
opcache.revalidate_freq=60
```

### MySQL Optimisation

```sql
-- Vérifier performances
SHOW VARIABLES LIKE 'innodb_buffer_pool_size';

-- Analyser queries lentes
SET GLOBAL slow_query_log = 'ON';
SET GLOBAL long_query_time = 2;
```

### Cache Redis (Optionnel)

```bash
# Installer Redis
sudo apt install redis-server php-redis

# Configurer dans config/config.php
define('REDIS_ENABLED', true);
define('REDIS_HOST', '127.0.0.1');
define('REDIS_PORT', 6379);
```

---

## 📞 Support

### En Cas de Problème

1. **Consultez** la documentation complète (README.md)
2. **Vérifiez** les logs d'erreur
3. **Testez** en mode debug
4. **Contactez** le support : support@marketflowpro.com

### Support Inclus

- ✅ **Email** : Réponse sous 24h
- ✅ **Chat** : Disponible 9h-18h
- ✅ **Documentation** : Complète et illustrée
- ✅ **Mises à jour** : Gratuites pendant 1 an

---

## ✅ Post-Installation

### Étapes Suivantes

1. ✅ Créer compte admin
2. ✅ Configurer paramètres site
3. ✅ Créer catégories produits
4. ✅ Tester inscription vendeur
5. ✅ Tester ajout produit
6. ✅ Tester achat complet
7. ✅ Vérifier emails
8. ✅ Tester webhooks Stripe
9. ✅ Personnaliser design
10. ✅ Lancer ! 🚀

---

**🎉 Félicitations ! Votre marketplace est opérationnelle !**

*Besoin d'aide ? → support@marketflowpro.com*