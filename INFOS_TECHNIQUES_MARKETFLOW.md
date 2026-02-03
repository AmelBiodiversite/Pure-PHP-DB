# 📋 INFORMATIONS TECHNIQUES - MARKETFLOW PRO

**Date :** 1er février 2026  
**Développeur :** Amel Benmaamar  
**Projet :** MarketFlow Pro - Plateforme e-commerce PHP

---

## 🖥️ ENVIRONNEMENT DE DÉVELOPPEMENT

### Système
- **OS :** ChromeOS avec conteneur Linux (Crostini)
- **Distribution Linux :** Debian/Ubuntu sur Chromebook
- **Terminal :** Bash

### Localisation des fichiers
- **Projet actif :** `/var/www/html/Pure-PHP-DB`
- **Lien symbolique :** `~/MonProjetActif` → `/var/www/html/Pure-PHP-DB`
- **Ancien projet (à ignorer) :** `~/Pure-PHP-DB` (15 janvier)

⚠️ **Important :** L'explorateur ChromeOS ne montre que `/home/amelbenmaamar/`. Pour accéder au projet depuis l'explorateur, utilisez le lien `MonProjetActif`.

---

## 🗄️ BASE DE DONNÉES

### Configuration PostgreSQL Locale
```
DB_HOST=localhost
DB_PORT=5432
DB_DATABASE=heliumdb
DB_USERNAME=postgres
DB_PASSWORD=password
```

### Fichier de configuration
- **Emplacement :** `/var/www/html/Pure-PHP-DB/.env`
- **Connection string :** `postgresql://postgres:password@localhost:5432/heliumdb`

### Commandes utiles
```bash
# Se connecter à la base
psql -U postgres -d heliumdb

# Voir les tables
psql -U postgres -d heliumdb -c "\dt"

# Voir les utilisateurs
psql -U postgres -d heliumdb -c "SELECT id, username, email, role FROM users;"
```

### Fichiers SQL disponibles
- `/var/www/html/Pure-PHP-DB/database/FULL_IMPORT.sql` (export complet)
- `/var/www/html/Pure-PHP-DB/database/schema.sql` (structure)
- `/var/www/html/Pure-PHP-DB/database/marketflow_production.sql`

---

## 👤 UTILISATEURS DE TEST

### Admin Local
- **Email :** admin@marketflow.com
- **Password :** admin123
- **Role :** admin

### Autres utilisateurs
```sql
-- Seller
Email: seller@marketflow.com
Role: seller

-- Buyer  
Email: buyer@marketflow.com
Role: buyer
```

---

## 🌐 DÉPLOIEMENT

### Production (Railway)
- **URL :** https://astonishing-nurturing-production.up.railway.app
- **URL Admin :** https://astonishing-nurturing-production.up.railway.app/admin
- **Plateforme :** Railway
- **Branche Git :** main

### Repository Git
- **URL :** [À compléter avec l'URL de votre repo GitHub]
- **Branche principale :** main

---

## 🐛 PROBLÈMES RÉSOLUS (ajouter les suivants au fur et à mesure)

### 1. Erreur `getSecurityStats()` 
**Symptôme :** `Fatal error: Call to undefined function getSecurityStats()`  
**Cause :** Fonction manquante dans `app/helpers/security_helper.php`  
**Solution :** Ajout de la fonction dans le fichier

### 2. Connexion locale impossible
**Symptôme :** Email/mot de passe incorrect  
**Cause :** Hash de mot de passe invalide  
**Solution :** 
```bash
NEW_HASH=$(php -r "echo password_hash('admin123', PASSWORD_DEFAULT);")
psql -U postgres -d heliumdb -c "UPDATE users SET password = '$NEW_HASH' WHERE username = 'admin';"
```

### 3. Fichiers introuvables dans l'explorateur ChromeOS
**Cause :** ChromeOS ne montre que `/home/amelbenmaamar/`, pas `/var/www/html/`  
**Solution :** Création d'un lien symbolique `MonProjetActif`

---

## 📁 STRUCTURE DU PROJET
```
Pure-PHP-DB/
├── app/
│   ├── controllers/      # Contrôleurs (AdminController, AuthController, etc.)
│   ├── models/          # Modèles (User, Product, Order, etc.)
│   ├── views/           # Vues (admin/, auth/, products/, etc.)
│   └── helpers/         # Helpers
│       ├── auth_helper.php
│       ├── functions.php
│       ├── security_helper.php  ⭐ Contient getSecurityStats()
│       └── SecurityHelper.php
├── config/              # Configuration
│   ├── config.php
│   ├── routes.php
│   ├── session.php
│   └── security_headers.php
├── core/                # Framework core
│   ├── Database.php     # Connexion PostgreSQL
│   ├── Router.php
│   ├── Controller.php
│   ├── Model.php
│   └── SecurityLogger.php
├── data/
│   ├── logs/
│   │   └── security.log
│   └── marketflow.db
├── database/            # Fichiers SQL
├── public/              # Point d'entrée web
│   ├── index.php
│   ├── css/
│   ├── js/
│   └── uploads/
├── .env                 # Variables d'environnement ⭐
└── index.php            # Point d'entrée principal
```

---

## 🔧 COMMANDES UTILES

### Démarrer le serveur local
```bash
cd /var/www/html/Pure-PHP-DB
php -S localhost:8000 -t public
```
Accès : http://localhost:8000

### Git - Pousser les changements
```bash
cd /var/www/html/Pure-PHP-DB
git status
git add .
git commit -m "Votre message"
git push origin main
```

### Réinitialiser mot de passe admin
```bash
NEW_HASH=$(php -r "echo password_hash('admin123', PASSWORD_DEFAULT);")
psql -U postgres -d heliumdb -c "UPDATE users SET password = '$NEW_HASH' WHERE username = 'admin';"
```

### Vérifier les logs de sécurité
```bash
tail -f data/logs/security.log
```

---

## ⚠️ NOTES IMPORTANTES

1. **Toujours travailler dans** `/var/www/html/Pure-PHP-DB` (pas dans `~/Pure-PHP-DB`)
2. **Fichier .env** ne doit JAMAIS être commité sur Git (déjà dans .gitignore)
3. **Railway utilise** `DATABASE_URL` (détecté automatiquement)
4. **ChromeOS** : Utiliser le terminal pour accéder à `/var/www/html/`, l'explorateur ne le montre pas
5. **SecurityHelper.php vs security_helper.php** : Les deux existent, `security_helper.php` est chargé dans `index.php`

---

## 📞 CONTACTS & RESSOURCES

- **Email sécurité :** marketflow.fr@gmail.com
- **Documentation PostgreSQL :** https://www.postgresql.org/docs/
- **Railway Docs :** https://docs.railway.app/

