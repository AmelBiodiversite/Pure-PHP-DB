# 🚀 MarketPlace Pro — Marketplace Multi-Vendeur PHP

**Marketplace multi-vendeurs prête pour la production** | PHP/PostgreSQL | 23 000+ lignes

## 🎨 Captures d'Écran

### Page d'accueil
![Homepage](docs/screenshots/homepage.png)

### Dashboard Admin
![Admin](docs/screenshots/admin_dashboard.png)

### Page Catégories
![Categories](docs/screenshots/categories.png)

### Dashboard Sécurité
![Security](docs/screenshots/security_dashboard.png)

---

## 🎯 Qu'est-ce que MarketPlace Pro ?

Une plateforme marketplace **complète, sécurisée et évolutive** pour la vente de produits digitaux (templates, ebooks, formations, etc.) avec système de commission automatique et paiements Stripe intégrés.

**Parfait pour :**
- 🏢 **Agences web** développant des solutions marketplace pour leurs clients
- 💼 **Développeurs freelance** économisant 3 mois de développement
- 🚀 **Entrepreneurs** lançant leur marketplace rapidement

🔗 **Démo live : [www.marketflow.fr](https://www.marketflow.fr)**

---

## ✨ Fonctionnalités

### 🛍️ Pour les Acheteurs
- Authentification sécurisée & profils utilisateurs
- Catalogue avec filtres (catégories, prix, recherche)
- Panier avec codes promo
- Paiement Stripe intégré
- Téléchargements (3x par produit)
- Historique commandes & factures
- Système d'avis et de notes
- Liste de souhaits (wishlist)

### 💰 Pour les Vendeurs
- Dashboard vendeur avec analytics
- Upload produits (fichiers + images)
- Statistiques de ventes avec graphiques Chart.js
- Commission configurable
- Gestion des avis clients

### 👑 Pour les Administrateurs
- Dashboard admin global
- Validation/rejet produits
- Gestion utilisateurs
- Modération des avis
- Statistiques globales
- **Dashboard de monitoring sécurité en temps réel (unique)**

---

## 🔒 Système de Sécurité Avancé

- 📊 Monitoring en temps réel (tentatives CSRF, XSS, SQLi)
- 📈 Statistiques sur 7 jours avec graphiques interactifs
- 🚨 Détection automatique des IPs suspectes
- 📧 Alertes email si > 5 événements critiques/heure
- ✅ 100% des formulaires protégés (CSRF tokens)
- ✅ 139 requêtes préparées (zéro injection SQL)
- ✅ Rate limiting sur 6 endpoints
- ✅ Détection session hijacking

---

## 🛠️ Stack Technique

- **PHP 8.2** — Architecture MVC sans framework
- **PostgreSQL 12+** — 139 requêtes préparées
- **Stripe** — Checkout, webhooks, remboursements
- **JavaScript Vanilla** — Aucune dépendance framework
- **Chart.js** — Dashboards analytics
- **Docker ready** — Déploiement en < 15 minutes

---

## 📊 Statistiques du Code

| Métrique | Valeur |
|----------|--------|
| Lignes PHP | 23 349 |
| Fichiers PHP | 81 |
| Contrôleurs | 15 |
| Vues | 36 |
| Requêtes préparées | 139 |
| Tests unitaires | 4 (PHPUnit) |

---

## 📦 Installation

### Prérequis
- PHP >= 8.2
- PostgreSQL >= 12
- Composer
- Compte Stripe

### Étapes

**1. Cloner le repo**
```bash
git clone git@github.com:VotreCompte/marketPlace.git
cd marketPlace
```

**2. Installer les dépendances**
```bash
composer install
```

**3. Configurer les variables d'environnement**
```bash
cp .env.example .env
nano .env
```

Remplir dans `.env` :
```
DATABASE_URL=postgresql://user:password@host:5432/dbname
STRIPE_PUBLIC_KEY=pk_live_...
STRIPE_SECRET_KEY=sk_live_...
STRIPE_WEBHOOK_SECRET=whsec_...
APP_SECRET=votre_secret_aleatoire
```

**4. Créer la base de données**
```bash
psql $DATABASE_URL < database/schema.sql
psql $DATABASE_URL < database/security-schema.sql
```

**5. Lancer en local**
```bash
cd public
php -S localhost:8000
```

---

## 🚀 Déploiement Railway

1. Créer un projet Railway
2. Ajouter un service PostgreSQL
3. Copier les variables d'environnement
4. `git push` — Railway déploie automatiquement via le Dockerfile inclus

---

## 💰 Licence Commerciale

**Inclus avec l'achat :**
- ✅ Accès code source complet
- ✅ Droits d'utilisation illimités pour vos projets clients
- ✅ Modifications autorisées
- ✅ Usage commercial autorisé

**Non inclus :**
- ❌ Revente du code source interdite
- ❌ Distribution gratuite interdite

---

## 🎯 Calcul du ROI

| Composant | Heures dev | Taux (50€/h) | Valeur |
|-----------|------------|--------------|--------|
| Backend (23k lignes) | 150h | 50€ | 7 500€ |
| Système Sécurité | 30h | 50€ | 1 500€ |
| Intégration Stripe | 20h | 50€ | 1 000€ |
| Dashboard Admin | 40h | 50€ | 2 000€ |
| Frontend/UI | 60h | 50€ | 3 000€ |
| **TOTAL** | **300h** | | **15 000€** |

**Votre prix : 2 997€ = 80% d'économie = 12 000€ économisés**

---

## 📞 Contact

**Créateur :** A. Devance — Développeur Full-Stack

📧 **Email :** contact@marketflow.fr
🔗 **Démo :** [www.marketflow.fr](https://www.marketflow.fr)

---

## 📄 Licence

Licence Commerciale — voir [CONTRAT_LICENCE.md](CONTRAT_LICENCE.md)

