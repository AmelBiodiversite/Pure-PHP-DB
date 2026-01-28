# ❓ FAQ - MARKETFLOW PRO
## Questions Fréquentes des Acheteurs

**Dernière mise à jour:** Janvier 2026

---

## 💰 ACHAT & LICENCE

### Q1: Quel est le prix exact de MarketFlow Pro?

**R:** Nous proposons 3 packages:
- **Starter:** 8 000€ (code + support 30j + 1 session installation)
- **Business:** 12 000€ (Starter + support 60j + customisation branding + formation) ⭐ RECOMMANDÉ
- **Enterprise:** 18 000€ (Business + support 90j + 20h dev custom + audit sécu)

Prix SANS FRAIS RÉCURRENTS - paiement unique, licence à vie.

### Q2: Qu'est-ce qu'une licence "non-exclusive"?

**R:** Cela signifie:
- ✅ **Vous:** Pouvez utiliser, modifier, déployer commercialement sur 1 domaine
- ✅ **Nous:** Pouvons vendre à d'autres clients (d'où le prix accessible)
- ❌ **Vous:** Ne pouvez PAS revendre le code ou créer un produit concurrent
- ❌ **Vous:** Ne pouvez PAS utiliser sur plusieurs domaines (1 licence = 1 domaine)

C'est le modèle qui permet un prix 70% inférieur à une licence exclusive.

### Q3: Y a-t-il des frais cachés ou récurrents?

**R:** NON. Absolument aucun frais caché:
- ❌ Pas de frais mensuels/annuels
- ❌ Pas de commission sur vos ventes
- ❌ Pas de frais par transaction
- ❌ Pas de frais par utilisateur
- ✅ Hébergement: à votre charge (votre serveur, votre choix)
- ✅ Stripe: leurs frais standard (~1,4% + 0,25€) directement avec eux

**Optionnel (si vous voulez):**
- Support étendu: 500€/mois
- Développements custom: 80€/heure

### Q4: Puis-je obtenir une licence exclusive?

**R:** Oui, mais le prix serait différent:
- **Licence exclusive:** 150 000€ - 200 000€
- Inclut: Tous droits, code source, documentation, support 1 an
- Vous seriez le seul propriétaire du produit

Pour 99% des clients, la licence non-exclusive est le meilleur choix.

### Q5: Proposez-vous un essai gratuit?

**R:** Nous ne proposons pas de période d'essai car vous recevez le code source complet. En revanche:
- ✅ **Démo live gratuite** à tester pendant 7 jours (backend admin + seller dashboard)
- ✅ **Appel découverte gratuit** (30-45 min) pour discuter de votre projet
- ✅ **Documentation complète** accessible avant achat
- ✅ **Vidéos de démonstration** disponibles

Une fois le code livré, aucun remboursement n'est possible (propriété intellectuelle).

### Q6: Quels modes de paiement acceptez-vous?

**R:**
- ✅ Virement bancaire SEPA (France/Europe) - **RECOMMANDÉ**
- ✅ PayPal (frais PayPal à votre charge: +3,4%)
- ✅ Stripe (pour cartes bancaires internationales)

Paiement en 1 fois ou 2 fois (50% à la signature, 50% à la livraison).

---

## 🛠️ TECHNIQUE & INSTALLATION

### Q7: Quels sont les prérequis techniques?

**R:** Configuration minimale:
- **Serveur:** VPS ou dédié (shared hosting déconseillé)
- **PHP:** 8.0 ou supérieur
- **Base de données:** MySQL 5.7+ OU PostgreSQL 10+
- **Serveur web:** Apache 2.4+ OU Nginx 1.18+
- **RAM:** Minimum 2GB (4GB recommandé)
- **Espace disque:** 5GB minimum (+ espace pour uploads)
- **HTTPS:** Certificat SSL/TLS (Let's Encrypt gratuit OK)

Configuration recommandée pour trafic moyen (1000+ visiteurs/jour):
- VPS 4 CPU / 8GB RAM
- MySQL/PostgreSQL dédié
- PHP 8.2 avec OPcache activé

### Q8: Est-ce difficile à installer?

**R:** NON, si vous avez des compétences PHP/MySQL de base.

**Installation typique:**
1. Upload fichiers sur serveur (FTP/SSH)
2. Créer base de données MySQL
3. Importer le schéma SQL fourni
4. Configurer fichier `.env` (5 min)
5. Configurer serveur web (Apache/Nginx)
6. C'est prêt!

**Temps total:** 1-2 heures pour un développeur expérimenté

**Session d'installation incluse:** Tous nos packages incluent une session d'aide (2-4h selon package).

### Q9: Faut-il savoir coder pour utiliser MarketFlow Pro?

**R:** Ça dépend de votre rôle:

**Pour utiliser (admin/vendeur):** NON
- Interface graphique complète
- Aucun code nécessaire pour usage quotidien
- Ajout produits, gestion commandes, analytics: tout en interface

**Pour installer:** OUI (niveau intermédiaire)
- Connaissances PHP, MySQL, serveur web nécessaires
- OU engager un développeur pour l'installation (1-2h)

**Pour personnaliser:** OUI (niveau avancé)
- PHP, HTML, CSS, JavaScript
- Architecture MVC à comprendre
- Documentation complète fournie

### Q10: Puis-je tester sur mon serveur local d'abord?

**R:** Absolument! C'est même recommandé.

**Environnement de développement:**
- XAMPP, WAMP, MAMP, Laragon
- Docker (Dockerfile inclus)
- Vagrant (configuration possible)

Vous pouvez tester en local autant que vous voulez, la licence couvre 1 domaine de production.

### Q11: Est-ce compatible avec mon hébergeur?

**R:** MarketFlow Pro fonctionne sur la plupart des hébergeurs modernes:

**✅ Compatible:**
- OVH, Hostinger, PlanetHoster (VPS/dédié)
- DigitalOcean, Vultr, Linode
- AWS, Google Cloud, Azure
- Serveurs dédiés Hetzner, Scaleway
- Railway, Heroku (avec adaptations)

**❌ Non compatible:**
- Hébergements mutualisés bon marché (1€/mois)
- Hébergeurs sans accès SSH
- Serveurs PHP < 8.0
- Hébergements Windows (IIS non testé)

**Besoin d'aide?** On peut recommander des hébergeurs selon votre budget (5-50€/mois).

---

## 🎨 PERSONNALISATION

### Q12: Puis-je modifier le design et les couleurs?

**R:** OUI, à 100%!

**Changements faciles (sans coder):**
- Logo (remplacer fichier image)
- Couleurs principales (fichier CSS variables)
- Textes et traductions

**Changements avancés (avec code):**
- Structure HTML complète
- Style CSS complet
- JavaScript
- Emails transactionnels

Code source complet = personnalisation illimitée.

**Package Business inclut:** Customisation branding (logo, couleurs) faite pour vous.

### Q13: Puis-je ajouter des fonctionnalités personnalisées?

**R:** OUI, absolument!

**Vous-même (si développeur):**
- Code source complet disponible
- Architecture MVC claire et documentée
- Ajoutez ce que vous voulez

**Par nous (développements custom):**
- Inclus dans package Enterprise (20h)
- OU à l'heure: 80€/heure
- Exemples: intégrations API, features métier spécifiques

**Par un prestataire externe:**
- Vous êtes propriétaire du code
- N'importe quel développeur PHP peut travailler dessus
- Documentation complète fournie

### Q14: Comment ajouter une nouvelle langue?

**R:** Processus en 3 étapes:

**1. Fichiers de langue (1-2 jours):**
- Copier `/app/lang/fr.php` → `/app/lang/en.php`
- Traduire toutes les chaînes (~500 phrases)

**2. Interface (1 jour):**
- Ajouter sélecteur de langue dans header
- Gérer changement de langue en session

**3. Contenu BDD (variable):**
- Catégories, pages, etc. en nouvelle langue

**OU commander l'add-on:** Multi-langues (FR/EN/ES) pour 3 000€ (tout fait).

---

## 🔒 SÉCURITÉ

### Q15: Est-ce sécurisé pour la production?

**R:** OUI, audit complet effectué. MarketFlow Pro inclut:

**Protections actives:**
- ✅ CSRF (Cross-Site Request Forgery)
- ✅ XSS (Cross-Site Scripting)
- ✅ SQL Injection (requêtes préparées)
- ✅ Rate Limiting (anti-brute force)
- ✅ Headers de sécurité HTTP complets
- ✅ Sessions sécurisées (httponly, secure, samesite)
- ✅ Hachage bcrypt des mots de passe

**Monitoring:**
- ✅ Dashboard sécurité dédié
- ✅ Logs de sécurité avec rotation
- ✅ Alertes sur activités suspectes

**Conformité:**
- ✅ OWASP Top 10 respecté
- ✅ PCI-DSS compliant (via Stripe)

Rapport d'audit complet disponible dans `SECURITY_AUDIT_REPORT.md`.

### Q16: Les paiements sont-ils sécurisés?

**R:** OUI, totalement.

**Stripe Integration:**
- PCI-DSS Level 1 certified (plus haut niveau)
- Aucune donnée bancaire stockée sur vos serveurs
- Paiements via Stripe Checkout (hébergé par Stripe)
- 3D Secure supporté automatiquement
- Webhooks avec signatures vérifiées

**Vous n'avez JAMAIS les données bancaires = 0 risque PCI**

Stripe gère 100% de la sécurité des paiements.

### Q17: Mes données sont-elles protégées?

**R:** OUI, plusieurs niveaux:

**Base de données:**
- Mots de passe hachés (bcrypt, coût 12)
- Données sensibles séparées
- Accès BDD restreint par permissions

**Fichiers:**
- Fichiers produits HORS webroot (non accessibles directement)
- Téléchargements via script avec authentification
- Validation stricte des uploads

**RGPD:**
- Consentement utilisateur géré
- Export de données possible
- Suppression de compte disponible

### Q18: Que se passe-t-il si je trouve une faille de sécurité?

**R:**

**Pendant période de support (30-90j):**
- Signaler immédiatement par email sécurisé
- Correction sous 48-72h si critique
- Patch fourni gratuitement

**Après période de support:**
- Signalement apprécié (responsable disclosure)
- Correction fournie si possible
- OU vous corrigez (vous avez le code source)

**Bug Bounty:** Pas de programme officiel actuellement, mais signalements sérieux récompensés.

---

## 💳 PAIEMENTS & STRIPE

### Q19: Dois-je avoir un compte Stripe?

**R:** OUI, obligatoire pour accepter les paiements.

**Créer un compte Stripe:**
- Gratuit: https://stripe.com
- Disponible dans 40+ pays
- Activation: 1-2 jours (vérifications)
- Mode test disponible immédiatement

**Frais Stripe (standard):**
- Europe: 1,4% + 0,25€ par transaction
- International: 2,9% + 0,25€
- Pas de frais mensuels avec Stripe

**Alternatives:** PayPal disponible en add-on (2 000€).

### Q20: Comment fonctionne le système de commission?

**R:** Split payment automatique intégré:

**Configuration (dans admin):**
- Définir commission plateforme (ex: 10%)
- S'applique automatiquement à toutes les ventes

**Fonctionnement:**
- Client paie 100€ pour un produit
- Stripe prend 1,4% + 0,25€ = 1,65€
- Reste: 98,35€
- Commission plateforme 10% (sur prix original) = 10€ (va sur votre compte Stripe)
- Vendeur reçoit: 88,35€ sur son compte Stripe
- Note: La commission peut être calculée sur le montant brut (100€) ou net (98,35€) selon configuration

**Payouts vendeurs:**
- Configuration par vendeur (compte Stripe connecté)
- Automatique ou manuel
- Dashboard vendeur avec historique complet

### Q21: Puis-je vendre dans plusieurs devises?

**R:** Actuellement: 1 devise par installation (EUR par défaut).

**Pour multi-devises:**
- Add-on disponible en développement (date TBD)
- OU développement custom (estimation: 15-20h = 1 200-1 600€)

Stripe supporte 135+ devises, c'est juste l'interface à adapter.

---

## 📦 FONCTIONNALITÉS

### Q22: Quels types de produits digitaux puis-je vendre?

**R:** Tous types de fichiers téléchargeables:

**Formats supportés:**
- ✅ PDF, EPUB (eBooks, documents)
- ✅ ZIP, RAR (templates, code source)
- ✅ JPG, PNG, SVG (images, designs)
- ✅ MP4, MOV (vidéos)
- ✅ MP3, WAV (audio)
- ✅ EXE, APP (logiciels - avec disclaimers)
- ✅ N'importe quel format de fichier

**Limitations:**
- Taille max par fichier: Configurable (100MB par défaut)
- Nombre de fichiers par produit: Illimité
- Espace total: Selon votre serveur

**Non adapté pour:**
- ❌ Produits physiques (pas de gestion shipping)
- ❌ Services de freelance (pas de système de tickets)
- ❌ Réservations/bookings (pas de calendrier)

### Q23: Combien de vendeurs puis-je avoir?

**R:** ILLIMITÉ!

Il n'y a aucune limitation sur:
- Nombre de vendeurs
- Nombre de produits
- Nombre de commandes
- Nombre d'acheteurs

Seules limites = votre serveur (CPU/RAM/stockage).

**Scalabilité testée:**
- 1000 vendeurs actifs: VPS 4CPU/8GB OK
- 10 000+ produits: Index BDD optimisés
- 100 000+ utilisateurs: Architecture permet

### Q24: Y a-t-il un système d'abonnements (recurring)?

**R:** NON, pas dans la version actuelle.

**Actuellement supporté:**
- ✅ Paiements uniques
- ✅ Codes promo
- ✅ Prix personnalisés

**Pas supporté (actuellement):**
- ❌ Abonnements mensuels/annuels
- ❌ Paiements récurrents automatiques

**Solution:**
- Utiliser plusieurs produits (Abonnement 1 mois, Abonnement 12 mois)
- OU développement custom (estimation: 30-40h)

Roadmap: Abonnements prévus en v1.2 (Q3 2026).

### Q25: Puis-je offrir des produits gratuits?

**R:** OUI, c'est possible:

**Configuration:**
- Prix à 0,00€
- Processus checkout simplifié (pas de Stripe)
- Téléchargement direct après inscription

**Cas d'usage:**
- Freemium (produit gratuit + upsell premium)
- Lead magnets
- Échantillons gratuits

---

## 🚀 SUPPORT & MISES À JOUR

### Q26: Qu'inclut le support technique?

**R:** Selon le package:

**Starter (30 jours):**
- Email uniquement
- Réponse < 48h ouvrées
- Aide installation/configuration
- Correction bugs critiques
- 1 session visio installation (2h)

**Business (60 jours):**
- Email prioritaire
- Réponse < 24h ouvrées
- + Formation admin (2h)
- + 2 sessions support (4h total)
- + Aide déploiement production

**Enterprise (90 jours):**
- Email ultra-prioritaire
- Réponse < 12h ouvrées (24/7 pour critiques)
- + Formation équipe (8h)
- + SLA 48h bugs critiques
- + Ligne directe

**Support N'INCLUT PAS:**
- Développements custom (sauf Enterprise: 20h incluses)
- Formation approfondie PHP/MySQL
- Maintenance serveur
- Optimisations performance personnalisées

### Q27: Puis-je prolonger le support?

**R:** OUI, support continu disponible:

**Support Mensuel:** 500€/mois
- Email prioritaire
- Réponse < 24h
- Corrections bugs
- Conseils techniques
- Sans engagement (résiliable chaque mois)

**Support Annuel:** 5 000€/an (économie 17%)
- Tout le support mensuel
- +4h développements custom/mois
- Accès prioritaire nouvelles features
- Audit sécurité annuel

### Q28: Vais-je recevoir des mises à jour?

**R:** OUI, mises à jour incluses selon package:

**Tous packages:**
- Corrections de bugs critiques: À vie
- Patches de sécurité: À vie
- Documentation mise à jour: À vie

**Mises à jour fonctionnelles:**
- 6 mois d'accès inclus (nouvelles features)
- Après: optionnel (voir Q29)

**Comment:** Accès GitHub privé pendant période, puis archives ZIP.

### Q29: Comment obtenir les nouvelles versions?

**R:**

**Pendant période incluse (6 mois):**
- Accès GitHub: git pull
- Notification email des releases
- Changelog détaillé

**Après 6 mois:**
- **Option 1:** Support continu (500€/mois) = mises à jour incluses
- **Option 2:** Paiement ponctuel par version majeure (~1 000-2 000€)
- **Option 3:** Rester sur votre version (fonctionnel à vie)

**Important:** Bugs critiques et sécurité = toujours gratuits.

### Q30: Que se passe-t-il après la période de support?

**R:** Vous êtes 100% autonome:

**Vous gardez:**
- ✅ Code source complet à vie
- ✅ Licence d'utilisation à vie
- ✅ Modifications illimitées
- ✅ Usage commercial à vie

**Vous n'avez plus:**
- ❌ Support email
- ❌ Mises à jour automatiques (sauf bugs critiques)
- ❌ Sessions de formation

**Vous pouvez:**
- ✅ Continuer à utiliser la version que vous avez (stable)
- ✅ Corriger vous-même les bugs (code source ouvert pour vous)
- ✅ Engager un développeur PHP externe
- ✅ Prolonger le support si besoin (voir Q27)

---

## 🎯 BUSINESS & ROI

### Q31: Quel ROI puis-je attendre?

**R:** Exemple de calcul conservateur:

**Investissement:**
- MarketFlow Pro Business: 12 000€
- Hébergement (1 an): 600€
- **Total:** 12 600€

**Revenus (commission 10%):**
- 50 vendeurs actifs
- 5 ventes/mois/vendeur en moyenne
- Panier moyen: 40€
- Commission: 50 × 5 × 40€ × 10% = **1 000€/mois**

**ROI:**
- Break-even: 12,6 mois
- Année 2: 12 000€ de profit
- Année 3: 12 000€ de profit
- ...

**Scalabilité:** Avec 200 vendeurs = 4 000€/mois = ROI en 3 mois!

### Q32: Combien coûte l'hébergement?

**R:** Budget hébergement recommandé:

**Startup (< 1000 visiteurs/jour):**
- VPS 2CPU/4GB: 15-30€/mois
- Exemples: OVH VPS, DigitalOcean Droplet
- **Annuel:** ~300€

**Croissance (1000-10 000 visiteurs/jour):**
- VPS 4CPU/8GB: 40-80€/mois
- + CDN optionnel: 20€/mois
- **Annuel:** ~700€

**Scale (> 10 000 visiteurs/jour):**
- Load balancer + multiple instances: 200€+/mois
- CDN obligatoire
- **Annuel:** ~2 500€+

**Total Cost of Ownership (3 ans):**
- MarketFlow: 12 000€
- Hébergement (moyen): 2 100€
- **Total: 14 100€** vs 40-80k€ développement custom

### Q33: Puis-je revendre ma marketplace?

**R:** Oui, mais avec conditions:

**Ce que vous pouvez revendre:**
- ✅ Votre instance complète (avec votre contenu)
- ✅ Votre base de clients/vendeurs
- ✅ Votre marque et domaine

**Ce que vous NE pouvez PAS revendre:**
- ❌ Le code source MarketFlow Pro seul
- ❌ Une "copie" vide pour un autre projet
- ❌ Template/thème basé sur MarketFlow

**En pratique:** Vous pouvez vendre votre business marketplace (comme n'importe quel business), mais pas le logiciel en lui-même.

---

## 🌍 INTERNATIONAL

### Q34: MarketFlow Pro est disponible dans quels pays?

**R:** Code disponible mondialement, mais:

**Langue interface:**
- Français (natif)
- Anglais/Espagnol: Add-on 3 000€

**Stripe disponible dans:**
- 🇫🇷 France, 🇧🇪 Belgique, 🇨🇭 Suisse, 🇨🇦 Canada
- 🇬🇧 UK, 🇩🇪 Allemagne, 🇪🇸 Espagne, 🇮🇹 Italie
- 🇺🇸 USA, + 35 autres pays
- Liste complète: https://stripe.com/global

**Si Stripe indisponible:** PayPal add-on disponible.

### Q35: Puis-je obtenir une facture?

**R:** OUI, automatiquement:

- Facture TTC (TVA applicable selon pays)
- Envoyée par email dans les 48h du paiement
- Format PDF
- Mentions légales complètes

**TVA:**
- France: 20%
- UE (pro avec VAT): 0% (autoliquidation)
- UE (particulier): TVA du pays
- Hors UE: 0%

---

## 📞 DIVERS

### Q36: Proposez-vous une démo personnalisée?

**R:** OUI, absolument!

**Démo Standard (Gratuite):**
- Accès démo live 7 jours
- Backend admin complet
- Seller dashboard
- Auto-service

**Démo Personnalisée (Gratuite):**
- Visio 30-60 min
- Tour guidé par expert
- Adapté à votre use case
- Q&A en direct
- Réservation: [calendrier]

### Q37: Puis-je voir des exemples de marketplaces utilisant MarketFlow?

**R:** Pour préserver la confidentialité de nos clients, nous ne publions pas de liste publique.

**En revanche:**
- Études de cas anonymisées disponibles
- Screenshots et vidéos de démo
- Témoignages clients (avec accord)

**Lors de la démo personnalisée**, nous pouvons montrer des exemples pertinents selon votre secteur.

### Q38: Offrez-vous des remises pour startups/associations?

**R:** OUI, programme spécial:

**Startups (< 2 ans, < 100k€ CA):**
- -20% sur package Business
- Soit 9 600€ au lieu de 12 000€

**Associations/ONG:**
- -30% sur tous packages
- Starter: 5 600€ au lieu de 8 000€

**Étudiants/Écoles:**
- Licence éducative spéciale
- Nous contacter pour tarif

**Conditions:** Justificatif requis, usage non-lucratif pour asso/écoles.

### Q39: Puis-je devenir revendeur/affilié?

**R:** OUI, deux programmes:

**Programme Affiliation:**
- 15% commission par vente
- Lien trackable unique
- Paiement à 30 jours
- Inscription: [lien]

**Programme Partenaire (Agences):**
- 20-25% commission par vente
- White-label possible
- Support dédié
- Formation incluse
- Minimum: 3 ventes/an
- Inscription: [lien]

### Q40: Comment vous contacter si j'ai d'autres questions?

**R:**

**Ventes & Questions générales:**
- 📧 Email: sales@marketflowpro.com
- 📞 Téléphone: +33 X XX XX XX XX
- 💬 Chat: marketflowpro.com (9h-18h CET)

**Support technique (clients uniquement):**
- 📧 Email: support@marketflowpro.com
- 🎫 Ticket: dashboard client

**Réseaux sociaux:**
- LinkedIn: /company/marketflowpro
- Twitter: @marketflowpro

**Délai de réponse:**
- Email: < 24h ouvrées
- Chat: Instantané (heures bureau)
- Téléphone: Immédiat

---

## 🎯 PRÊT À DÉMARRER?

### 3 Prochaines Étapes:

**1. Tester la Démo** 🖥️
- Accès gratuit 7 jours: [demo.marketflowpro.com]
- Backend complet
- Aucune carte requise

**2. Réserver un Appel** 📞
- 30 min avec un expert
- Discuter de votre projet
- Recommandation package
- Calendrier: [lien]

**3. Demander un Devis** 📄
- Proposition personnalisée
- Timeline détaillée
- ROI estimé
- Formulaire: [lien]

---

**Vous ne trouvez pas votre question?**  
Contactez-nous: sales@marketflowpro.com

*FAQ v1.0 - Janvier 2026*  
*40 questions les plus fréquentes de nos prospects*
