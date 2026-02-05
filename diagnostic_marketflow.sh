#!/bin/bash
# Script de diagnostic MARKETFLOW - Version personnalisée
# Analyse ton projet Pure-PHP-DB

cd /var/www/html/Pure-PHP-DB

echo "🔍 DIAGNOSTIC MARKETFLOW (Architecture MVC Custom)"
echo "=================================================="
echo ""

# Couleurs
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# ==========================================
# 1. STRIPE (CRITIQUE POUR VENTES)
# ==========================================
echo -e "${BLUE}💳 1. CONFIGURATION STRIPE${NC}"
echo "---"

if [ -f .env ]; then
    # Vérifier présence des clés
    for KEY in "STRIPE_PUBLIC_KEY" "STRIPE_SECRET_KEY" "STRIPE_WEBHOOK_SECRET"; do
        if grep -q "^$KEY=" .env 2>/dev/null; then
            VALUE=$(grep "^$KEY=" .env | cut -d'=' -f2)
            if [ -z "$VALUE" ]; then
                echo -e "${RED}❌ $KEY: Défini mais VIDE${NC}"
            elif echo "$VALUE" | grep -q "_test_"; then
                echo -e "${YELLOW}⚠️  $KEY: MODE TEST (passer en PRODUCTION!)${NC}"
            else
                echo -e "${GREEN}✅ $KEY: Configuré${NC}"
            fi
        else
            echo -e "${RED}❌ $KEY: MANQUANT dans .env${NC}"
        fi
    done
    
    # Vérifier fallback dangereux dans config.php
    if grep -q "pk_test_YOUR_PUBLIC_KEY" config/config.php; then
        echo -e "${YELLOW}⚠️  Fallback 'YOUR_PUBLIC_KEY' détecté dans config.php${NC}"
        echo "   → Risque: App démarre avec clés invalides si .env absent"
    fi
else
    echo -e "${RED}❌ Fichier .env absent${NC}"
fi

echo ""

# ==========================================
# 2. BASE DE DONNÉES
# ==========================================
echo -e "${BLUE}🗄️  2. BASE DE DONNÉES${NC}"
echo "---"

if [ -f .env ]; then
    if grep -q "^DATABASE_URL=" .env; then
        DB_URL=$(grep "^DATABASE_URL=" .env | cut -d'=' -f2-)
        if [ -z "$DB_URL" ]; then
            echo -e "${RED}❌ DATABASE_URL: Vide${NC}"
        elif echo "$DB_URL" | grep -q "localhost\|127.0.0.1"; then
            echo -e "${YELLOW}⚠️  DATABASE_URL: localhost (dev uniquement)${NC}"
        else
            echo -e "${GREEN}✅ DATABASE_URL: Production distante${NC}"
        fi
        
        # Vérifier si schema.sql a été importé
        if [ -f database/schema.sql ]; then
            echo -e "${GREEN}✅ Schema SQL présent (database/schema.sql)${NC}"
        fi
    else
        echo -e "${RED}❌ DATABASE_URL: Manquant${NC}"
    fi
fi

echo ""

# ==========================================
# 3. PAGES LÉGALES (OBLIGATOIRES)
# ==========================================
echo -e "${BLUE}⚖️  3. PAGES LÉGALES (RGPD)${NC}"
echo "---"

# Chercher dans app/views/home/
LEGAL_DIR="app/views/home"
for PAGE in "cgv" "cgu" "mentions" "legal" "privacy" "confidentialite" "terms"; do
    if find $LEGAL_DIR -name "*$PAGE*.php" 2>/dev/null | grep -q .; then
        FILE=$(find $LEGAL_DIR -name "*$PAGE*.php" | head -1 | sed "s|^./||")
        echo -e "${GREEN}✅ $FILE${NC}"
    fi
done

# Vérifier ce qui manque
MISSING=()
find $LEGAL_DIR -name "*cgv*.php" &>/dev/null || MISSING+=("CGV (Conditions Générales de Vente)")
find $LEGAL_DIR -name "*cgu*.php" &>/dev/null || MISSING+=("CGU (Conditions Générales d'Utilisation)")
find $LEGAL_DIR -name "*mention*.php" -o -name "*legal*.php" &>/dev/null || MISSING+=("Mentions légales")

if [ ${#MISSING[@]} -gt 0 ]; then
    echo -e "${YELLOW}⚠️  Pages manquantes (OBLIGATOIRES) :${NC}"
    for PAGE in "${MISSING[@]}"; do
        echo "   - $PAGE"
    done
fi

echo ""

# ==========================================
# 4. EMAILS DE PRODUCTION
# ==========================================
echo -e "${BLUE}📧 4. CONFIGURATION EMAIL${NC}"
echo "---"

if grep -q "^SECURITY_ALERT_EMAIL=" .env 2>/dev/null; then
    echo -e "${GREEN}✅ SECURITY_ALERT_EMAIL: Défini${NC}"
else
    echo -e "${YELLOW}⚠️  SECURITY_ALERT_EMAIL: Non défini${NC}"
fi

# Chercher config SMTP
if grep -rq "smtp\|sendgrid\|mailgun\|ses" config/ --include="*.php" 2>/dev/null; then
    echo -e "${GREEN}✅ Configuration SMTP détectée${NC}"
else
    echo -e "${YELLOW}⚠️  Pas de config SMTP trouvée${NC}"
    echo "   → Comment sont envoyés les emails ? (SendGrid, Mailgun, AWS SES ?)"
fi

echo ""

# ==========================================
# 5. SÉCURITÉ FICHIERS
# ==========================================
echo -e "${BLUE}🔐 5. SÉCURITÉ FICHIERS${NC}"
echo "---"

# .env permissions
if [ -f .env ]; then
    PERMS=$(stat -c %a .env 2>/dev/null || stat -f %Lp .env 2>/dev/null)
    if [ "$PERMS" = "600" ]; then
        echo -e "${GREEN}✅ .env permissions: 600 (sécurisé)${NC}"
    else
        echo -e "${YELLOW}⚠️  .env permissions: $PERMS (recommandé: 600)${NC}"
        echo "   → Corriger avec: chmod 600 .env"
    fi
fi

# .env dans Git ?
if git ls-files --error-unmatch .env &>/dev/null; then
    echo -e "${RED}🚨 DANGER: .env est dans Git!${NC}"
else
    echo -e "${GREEN}✅ .env non tracké par Git${NC}"
fi

echo ""

# ==========================================
# 6. UPLOADS & PERMISSIONS
# ==========================================
echo -e "${BLUE}📤 6. DOSSIERS UPLOADS${NC}"
echo "---"

for DIR in "public/uploads" "public/uploads/products" "public/uploads/products/files"; do
    if [ -d "$DIR" ]; then
        PERMS=$(stat -c %a "$DIR" 2>/dev/null || stat -f %Lp "$DIR" 2>/dev/null)
        if [ "$PERMS" = "755" ] || [ "$PERMS" = "775" ]; then
            echo -e "${GREEN}✅ $DIR: $PERMS (OK)${NC}"
        else
            echo -e "${YELLOW}⚠️  $DIR: $PERMS (recommandé: 755)${NC}"
        fi
    else
        echo -e "${RED}❌ $DIR: N'existe pas${NC}"
        echo "   → Créer avec: mkdir -p $DIR && chmod 755 $DIR"
    fi
done

echo ""

# ==========================================
# 7. DÉPENDANCES
# ==========================================
echo -e "${BLUE}📦 7. DÉPENDANCES${NC}"
echo "---"

if [ -d vendor ]; then
    echo -e "${GREEN}✅ Composer: vendor/ installé${NC}"
    # Vérifier Stripe
    if [ -d vendor/stripe ]; then
        echo -e "${GREEN}✅ Stripe PHP: Installé${NC}"
    else
        echo -e "${RED}❌ Stripe PHP: Manquant (lancer: composer install)${NC}"
    fi
else
    echo -e "${RED}❌ vendor/ absent (lancer: composer install)${NC}"
fi

if [ -d node_modules ]; then
    echo -e "${GREEN}✅ NPM: node_modules/ installé${NC}"
else
    echo -e "${YELLOW}⚠️  node_modules/ absent (lancer: npm install)${NC}"
fi

echo ""

# ==========================================
# 8. LOGS & MONITORING
# ==========================================
echo -e "${BLUE}📊 8. LOGS & MONITORING${NC}"
echo "---"

if [ -f data/logs/security.log ]; then
    SIZE=$(du -h data/logs/security.log | cut -f1)
    LINES=$(wc -l < data/logs/security.log)
    echo -e "${GREEN}✅ security.log: $SIZE ($LINES lignes)${NC}"
else
    echo -e "${YELLOW}⚠️  security.log: Absent${NC}"
fi

# Vérifier SecurityLogger est actif
if grep -rq "SecurityLogger" app/controllers/ 2>/dev/null; then
    echo -e "${GREEN}✅ SecurityLogger: Utilisé dans controllers${NC}"
fi

echo ""

# ==========================================
# 9. STRUCTURE CRITIQUE
# ==========================================
echo -e "${BLUE}🏗️  9. STRUCTURE CRITIQUE${NC}"
echo "---"

CRITICAL_FILES=(
    "index.php"
    "public/index.php"
    "config/config.php"
    "config/routes.php"
    "core/Router.php"
    "core/Database.php"
    "core/CSRF.php"
    "app/controllers/StripeController.php"
)

for FILE in "${CRITICAL_FILES[@]}"; do
    if [ -f "$FILE" ]; then
        echo -e "${GREEN}✅ $FILE${NC}"
    else
        echo -e "${RED}❌ $FILE: MANQUANT${NC}"
    fi
done

echo ""

# ==========================================
# RÉCAPITULATIF
# ==========================================
echo "=================================================="
echo -e "${BLUE}📋 RÉCAPITULATIF${NC}"
echo ""

echo -e "${RED}🚨 CRITIQUES (empêchent mise en prod):${NC}"
CRITIQUES=0

[ ! -f .env ] && echo "  - .env absent" && CRITIQUES=$((CRITIQUES+1))
! grep -q "^STRIPE_SECRET_KEY=" .env 2>/dev/null && echo "  - Clés Stripe non configurées" && CRITIQUES=$((CRITIQUES+1))
git ls-files --error-unmatch .env &>/dev/null && echo "  - .env dans Git" && CRITIQUES=$((CRITIQUES+1))

[ $CRITIQUES -eq 0 ] && echo "  Aucun problème critique ! ✅"

echo ""
echo -e "${YELLOW}⚠️  IMPORTANTS (à corriger rapidement):${NC}"

! find app/views/home -name "*cgv*.php" &>/dev/null && echo "  - Page CGV manquante"
! find app/views/home -name "*cgu*.php" &>/dev/null && echo "  - Page CGU manquante"
[ ! -d node_modules ] && echo "  - node_modules non installé"

echo ""
echo "=================================================="
echo -e "${GREEN}✅ Diagnostic terminé!${NC}"
echo ""
echo "Actions prioritaires:"
echo "1. Ajouter clés Stripe dans .env"
echo "2. Créer pages CGV/CGU/Mentions légales"
echo "3. Configurer emails production (SMTP)"
echo "4. Tester un achat complet (test -> prod)"
echo ""
