#!/bin/bash

# ================================================================
# MARKETFLOW PRO - AUDIT COMPLET DE SÉCURITÉ & QUALITÉ
# ================================================================
# Détecte erreurs critiques, failles de sécurité, problèmes de code
# Version : 1.0
# Date : 23 janvier 2025
# ================================================================

# Couleurs pour output
RED='\033[0;31m'
YELLOW='\033[1;33m'
GREEN='\033[0;32m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Variables
PROJECT_DIR="${1:-/home/runner/workspace}"
REPORT_FILE="audit_marketflow_$(date +%Y%m%d_%H%M%S).txt"
CRITICAL_ISSUES=0
WARNINGS=0
INFO=0

echo "================================================================"
echo "🔍 AUDIT MARKETFLOW PRO - Analyse complète"
echo "================================================================"
echo ""
echo "📁 Projet analysé : $PROJECT_DIR"
echo "📄 Rapport : $REPORT_FILE"
echo ""

# Créer fichier de rapport
{
    echo "================================================================"
    echo "AUDIT MARKETFLOW PRO - $(date)"
    echo "================================================================"
    echo ""
} > "$REPORT_FILE"

# ================================================================
# FONCTION : Logger les résultats
# ================================================================
log_critical() {
    echo -e "${RED}[CRITIQUE]${NC} $1"
    echo "[CRITIQUE] $1" >> "$REPORT_FILE"
    ((CRITICAL_ISSUES++))
}

log_warning() {
    echo -e "${YELLOW}[AVERTISSEMENT]${NC} $1"
    echo "[AVERTISSEMENT] $1" >> "$REPORT_FILE"
    ((WARNINGS++))
}

log_info() {
    echo -e "${BLUE}[INFO]${NC} $1"
    echo "[INFO] $1" >> "$REPORT_FILE"
    ((INFO++))
}

log_success() {
    echo -e "${GREEN}[OK]${NC} $1"
    echo "[OK] $1" >> "$REPORT_FILE"
}

# ================================================================
# 1. VÉRIFICATION STRUCTURE DU PROJET
# ================================================================
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "📂 1. STRUCTURE DU PROJET"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
{
    echo ""
    echo "=========================================="
    echo "1. STRUCTURE DU PROJET"
    echo "=========================================="
    echo ""
} >> "$REPORT_FILE"

# Vérifier dossiers essentiels
REQUIRED_DIRS=("app/controllers" "app/models" "app/views" "config" "core" "public" "database")

for dir in "${REQUIRED_DIRS[@]}"; do
    if [ -d "$PROJECT_DIR/$dir" ]; then
        log_success "Dossier $dir existe"
    else
        log_critical "Dossier manquant : $dir"
    fi
done

# ================================================================
# 2. SÉCURITÉ - FICHIERS SENSIBLES EXPOSÉS
# ================================================================
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "🔒 2. SÉCURITÉ - Fichiers sensibles"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
{
    echo ""
    echo "=========================================="
    echo "2. SÉCURITÉ - FICHIERS SENSIBLES"
    echo "=========================================="
    echo ""
} >> "$REPORT_FILE"

# Vérifier .env dans public/
if [ -f "$PROJECT_DIR/public/.env" ]; then
    log_critical "DANGER : .env exposé dans public/ - Clés API accessibles !"
else
    log_success ".env non exposé dans public/"
fi

# Vérifier config.php dans public/
if [ -f "$PROJECT_DIR/public/config.php" ]; then
    log_critical "DANGER : config.php exposé dans public/"
else
    log_success "config.php non exposé"
fi

# Vérifier fichiers backup exposés
BACKUP_FILES=$(find "$PROJECT_DIR/public" -type f \( -name "*.backup" -o -name "*.bak" -o -name "*.old" -o -name "*~" \) 2>/dev/null)
if [ -n "$BACKUP_FILES" ]; then
    log_warning "Fichiers backup exposés dans public/ :"
    echo "$BACKUP_FILES" | while read -r file; do
        echo "  - $file" | tee -a "$REPORT_FILE"
    done
else
    log_success "Pas de fichiers backup exposés"
fi

# ================================================================
# 3. SÉCURITÉ - INJECTIONS SQL
# ================================================================
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "💉 3. SÉCURITÉ - Injections SQL"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
{
    echo ""
    echo "=========================================="
    echo "3. INJECTIONS SQL"
    echo "=========================================="
    echo ""
} >> "$REPORT_FILE"

# Chercher requêtes SQL non préparées
UNSAFE_SQL=$(grep -rn "query.*\$_" "$PROJECT_DIR/app" --include="*.php" 2>/dev/null)
if [ -n "$UNSAFE_SQL" ]; then
    log_critical "INJECTIONS SQL POSSIBLES (query avec variables non échappées) :"
    echo "$UNSAFE_SQL" | head -10 | tee -a "$REPORT_FILE"
else
    log_success "Pas d'injection SQL évidente détectée"
fi

# Chercher concaténation SQL dangereuse
CONCAT_SQL=$(grep -rn 'query.*\"\$' "$PROJECT_DIR/app" --include="*.php" 2>/dev/null)
if [ -n "$CONCAT_SQL" ]; then
    log_warning "Concaténations SQL potentiellement dangereuses :"
    echo "$CONCAT_SQL" | head -10 | tee -a "$REPORT_FILE"
fi

# Compter requêtes préparées vs non préparées
PREPARED=$(grep -r "prepare(" "$PROJECT_DIR/app" --include="*.php" 2>/dev/null | wc -l)
DIRECT_QUERY=$(grep -r "->query(" "$PROJECT_DIR/app" --include="*.php" 2>/dev/null | wc -l)

echo "📊 Statistiques SQL :" | tee -a "$REPORT_FILE"
echo "   - Requêtes préparées : $PREPARED" | tee -a "$REPORT_FILE"
echo "   - Requêtes directes : $DIRECT_QUERY" | tee -a "$REPORT_FILE"

if [ "$DIRECT_QUERY" -gt 10 ]; then
    log_warning "Beaucoup de requêtes directes ($DIRECT_QUERY) - Utiliser plus de requêtes préparées"
fi

# ================================================================
# 4. SÉCURITÉ - XSS (Cross-Site Scripting)
# ================================================================
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "🕷️ 4. SÉCURITÉ - XSS (Cross-Site Scripting)"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
{
    echo ""
    echo "=========================================="
    echo "4. CROSS-SITE SCRIPTING (XSS)"
    echo "=========================================="
    echo ""
} >> "$REPORT_FILE"

# Chercher echo de variables non échappées
UNESCAPED_ECHO=$(grep -rn 'echo.*\$_\(GET\|POST\|REQUEST\)' "$PROJECT_DIR/app" --include="*.php" 2>/dev/null)
if [ -n "$UNESCAPED_ECHO" ]; then
    log_critical "XSS POSSIBLE - Variables affichées sans htmlspecialchars() :"
    echo "$UNESCAPED_ECHO" | head -10 | tee -a "$REPORT_FILE"
else
    log_success "Pas d'XSS évident détecté"
fi

# Vérifier utilisation de htmlspecialchars
HTMLSPECIALCHARS=$(grep -r "htmlspecialchars" "$PROJECT_DIR/app" --include="*.php" 2>/dev/null | wc -l)
echo "📊 Protection XSS : $HTMLSPECIALCHARS utilisations de htmlspecialchars()" | tee -a "$REPORT_FILE"

# ================================================================
# 5. AUTHENTIFICATION & SESSIONS
# ================================================================
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "🔐 5. AUTHENTIFICATION & SESSIONS"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
{
    echo ""
    echo "=========================================="
    echo "5. AUTHENTIFICATION & SESSIONS"
    echo "=========================================="
    echo ""
} >> "$REPORT_FILE"

# Vérifier si session_regenerate_id est utilisé
SESSION_REGEN=$(grep -r "session_regenerate_id" "$PROJECT_DIR/app" --include="*.php" 2>/dev/null | wc -l)
if [ "$SESSION_REGEN" -eq 0 ]; then
    log_warning "session_regenerate_id() non trouvé - Risque de fixation de session"
else
    log_success "Régénération de session trouvée ($SESSION_REGEN fois)"
fi

# Vérifier hachage des mots de passe
PASSWORD_HASH=$(grep -r "password_hash\|password_verify" "$PROJECT_DIR/app" --include="*.php" 2>/dev/null | wc -l)
if [ "$PASSWORD_HASH" -gt 0 ]; then
    log_success "Hachage sécurisé des mots de passe (password_hash/verify)"
else
    log_critical "password_hash/verify NON TROUVÉ - Mots de passe potentiellement non sécurisés !"
fi

# Chercher MD5/SHA1 pour mots de passe (dangereux)
WEAK_HASH=$(grep -rn "md5\|sha1" "$PROJECT_DIR/app" --include="*.php" 2>/dev/null | grep -i "password\|pass")
if [ -n "$WEAK_HASH" ]; then
    log_critical "HACHAGE FAIBLE (MD5/SHA1) détecté pour mots de passe :"
    echo "$WEAK_HASH" | tee -a "$REPORT_FILE"
fi

# ================================================================
# 6. UPLOAD DE FICHIERS
# ================================================================
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "📤 6. SÉCURITÉ - Upload de fichiers"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
{
    echo ""
    echo "=========================================="
    echo "6. UPLOAD DE FICHIERS"
    echo "=========================================="
    echo ""
} >> "$REPORT_FILE"

# Vérifier validation des extensions
UPLOAD_VALIDATION=$(grep -rn "ALLOWED_EXTENSIONS\|allowedExtensions\|mime" "$PROJECT_DIR/app" --include="*.php" 2>/dev/null | wc -l)
if [ "$UPLOAD_VALIDATION" -gt 0 ]; then
    log_success "Validation d'extensions de fichiers trouvée"
else
    log_warning "Validation d'extensions de fichiers non trouvée"
fi

# Chercher move_uploaded_file sans validation
UNSAFE_UPLOAD=$(grep -rn "move_uploaded_file" "$PROJECT_DIR/app" --include="*.php" 2>/dev/null)
if [ -n "$UNSAFE_UPLOAD" ]; then
    echo "📊 Fichiers gérant l'upload :" | tee -a "$REPORT_FILE"
    echo "$UNSAFE_UPLOAD" | tee -a "$REPORT_FILE"
fi

# ================================================================
# 7. GESTION DES ERREURS
# ================================================================
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "⚠️ 7. GESTION DES ERREURS"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
{
    echo ""
    echo "=========================================="
    echo "7. GESTION DES ERREURS"
    echo "=========================================="
    echo ""
} >> "$REPORT_FILE"

# Vérifier display_errors dans config
DISPLAY_ERRORS=$(grep -rn "display_errors.*=.*On\|display_errors.*=.*1" "$PROJECT_DIR/config" 2>/dev/null)
if [ -n "$DISPLAY_ERRORS" ]; then
    log_warning "display_errors activé - Ne JAMAIS activer en production !"
    echo "$DISPLAY_ERRORS" | tee -a "$REPORT_FILE"
fi

# Vérifier si error_reporting est configuré
ERROR_REPORTING=$(grep -r "error_reporting" "$PROJECT_DIR/config" --include="*.php" 2>/dev/null | wc -l)
if [ "$ERROR_REPORTING" -eq 0 ]; then
    log_warning "error_reporting non configuré"
else
    log_success "error_reporting configuré"
fi

# ================================================================
# 8. QUALITÉ DU CODE
# ================================================================
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "✨ 8. QUALITÉ DU CODE"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
{
    echo ""
    echo "=========================================="
    echo "8. QUALITÉ DU CODE"
    echo "=========================================="
    echo ""
} >> "$REPORT_FILE"

# Compter lignes de code
TOTAL_LINES=$(find "$PROJECT_DIR/app" -name "*.php" -type f -exec wc -l {} + 2>/dev/null | tail -1 | awk '{print $1}')
echo "📊 Total lignes de code : $TOTAL_LINES" | tee -a "$REPORT_FILE"

# Compter fichiers
TOTAL_FILES=$(find "$PROJECT_DIR/app" -name "*.php" -type f 2>/dev/null | wc -l)
echo "📁 Total fichiers PHP : $TOTAL_FILES" | tee -a "$REPORT_FILE"

# Chercher code commenté (TODO, FIXME)
TODOS=$(grep -rn "TODO\|FIXME\|XXX\|HACK" "$PROJECT_DIR/app" --include="*.php" 2>/dev/null | wc -l)
if [ "$TODOS" -gt 0 ]; then
    log_info "$TODOS commentaires TODO/FIXME trouvés"
fi

# Chercher fonctions trop longues (>100 lignes)
echo "🔍 Recherche de fonctions trop longues (>100 lignes)..." | tee -a "$REPORT_FILE"
find "$PROJECT_DIR/app" -name "*.php" -type f -exec awk '
    /function / { 
        fname=$0; 
        start=NR; 
        brace=0;
    }
    /{/ { brace++ }
    /}/ { 
        brace--;
        if (brace==0 && start>0) {
            len=NR-start;
            if (len>100) {
                print FILENAME ":" start " - Fonction trop longue (" len " lignes)";
            }
            start=0;
        }
    }
' {} \; 2>/dev/null | head -10 | tee -a "$REPORT_FILE"

# ================================================================
# 9. CONFIGURATION BASE DE DONNÉES
# ================================================================
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "🗄️ 9. CONFIGURATION BASE DE DONNÉES"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
{
    echo ""
    echo "=========================================="
    echo "9. BASE DE DONNÉES"
    echo "=========================================="
    echo ""
} >> "$REPORT_FILE"

# Vérifier utilisation de PDO
PDO_USAGE=$(grep -r "new PDO\|PDO::" "$PROJECT_DIR" --include="*.php" 2>/dev/null | wc -l)
if [ "$PDO_USAGE" -gt 0 ]; then
    log_success "Utilisation de PDO détectée"
else
    log_warning "PDO non trouvé - Utilisation de mysqli ?"
fi

# Chercher credentials en dur
HARDCODED_CREDS=$(grep -rn "password.*=.*['\"].\{3,\}['\"]" "$PROJECT_DIR/config" --include="*.php" 2>/dev/null | grep -v "PASSWORD")
if [ -n "$HARDCODED_CREDS" ]; then
    log_warning "Credentials potentiellement en dur dans config/ :"
    echo "$HARDCODED_CREDS" | tee -a "$REPORT_FILE"
fi

# ================================================================
# 10. DÉPENDANCES & COMPOSER
# ================================================================
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "📦 10. DÉPENDANCES & COMPOSER"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
{
    echo ""
    echo "=========================================="
    echo "10. DÉPENDANCES"
    echo "=========================================="
    echo ""
} >> "$REPORT_FILE"

if [ -f "$PROJECT_DIR/composer.json" ]; then
    log_success "composer.json trouvé"
    
    # Vérifier si vendor/ existe
    if [ -d "$PROJECT_DIR/vendor" ]; then
        log_info "vendor/ présent (dépendances installées)"
    else
        log_warning "vendor/ absent - Exécuter 'composer install'"
    fi
    
    # Afficher dépendances principales
    echo "📦 Dépendances principales :" | tee -a "$REPORT_FILE"
    grep -A 10 '"require"' "$PROJECT_DIR/composer.json" | head -15 | tee -a "$REPORT_FILE"
else
    log_warning "composer.json non trouvé"
fi

# ================================================================
# 11. STRIPE - CONFIGURATION PAIEMENTS
# ================================================================
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "💳 11. STRIPE - Configuration paiements"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
{
    echo ""
    echo "=========================================="
    echo "11. STRIPE PAIEMENTS"
    echo "=========================================="
    echo ""
} >> "$REPORT_FILE"

# Vérifier clés Stripe
STRIPE_KEYS=$(grep -rn "STRIPE.*KEY\|stripe.*key" "$PROJECT_DIR/config" --include="*.php" 2>/dev/null)
if [ -n "$STRIPE_KEYS" ]; then
    log_success "Configuration Stripe trouvée"
    
    # Vérifier si clés de test en production
    TEST_KEYS=$(grep -rn "sk_test_\|pk_test_" "$PROJECT_DIR/config" --include="*.php" 2>/dev/null)
    if [ -n "$TEST_KEYS" ]; then
        log_warning "Clés Stripe de TEST trouvées - Vérifier en production"
    fi
else
    log_info "Configuration Stripe non trouvée dans config/"
fi

# Vérifier webhooks
WEBHOOKS=$(grep -rn "webhook\|stripe.*event" "$PROJECT_DIR" --include="*.php" 2>/dev/null | wc -l)
if [ "$WEBHOOKS" -gt 0 ]; then
    log_success "Gestion des webhooks Stripe détectée"
else
    log_warning "Webhooks Stripe non trouvés"
fi

# ================================================================
# 12. PROTECTION CSRF
# ================================================================
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "🛡️ 12. PROTECTION CSRF"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
{
    echo ""
    echo "=========================================="
    echo "12. PROTECTION CSRF"
    echo "=========================================="
    echo ""
} >> "$REPORT_FILE"

# Chercher tokens CSRF
CSRF_TOKEN=$(grep -rn "csrf_token\|csrf.*token" "$PROJECT_DIR/app" --include="*.php" 2>/dev/null | wc -l)
if [ "$CSRF_TOKEN" -gt 0 ]; then
    log_success "Protection CSRF trouvée ($CSRF_TOKEN références)"
else
    log_warning "Protection CSRF non trouvée - Risque d'attaques CSRF"
fi

# ================================================================
# RÉSUMÉ FINAL
# ================================================================
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "📊 RÉSUMÉ DE L'AUDIT"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
{
    echo ""
    echo "=========================================="
    echo "RÉSUMÉ"
    echo "=========================================="
    echo ""
} >> "$REPORT_FILE"

echo "" | tee -a "$REPORT_FILE"
echo "🔴 Problèmes CRITIQUES : $CRITICAL_ISSUES" | tee -a "$REPORT_FILE"
echo "🟡 Avertissements       : $WARNINGS" | tee -a "$REPORT_FILE"
echo "🔵 Informations         : $INFO" | tee -a "$REPORT_FILE"
echo "" | tee -a "$REPORT_FILE"

# Score de sécurité
TOTAL_CHECKS=$((CRITICAL_ISSUES + WARNINGS))
if [ "$TOTAL_CHECKS" -eq 0 ]; then
    SECURITY_SCORE=100
elif [ "$CRITICAL_ISSUES" -eq 0 ]; then
    SECURITY_SCORE=$((100 - WARNINGS * 5))
else
    SECURITY_SCORE=$((100 - CRITICAL_ISSUES * 20 - WARNINGS * 5))
fi

[ "$SECURITY_SCORE" -lt 0 ] && SECURITY_SCORE=0

echo "🎯 Score de sécurité estimé : $SECURITY_SCORE/100" | tee -a "$REPORT_FILE"
echo "" | tee -a "$REPORT_FILE"

if [ "$SECURITY_SCORE" -ge 80 ]; then
    echo -e "${GREEN}✅ Excellent ! Votre code est bien sécurisé.${NC}" | tee -a "$REPORT_FILE"
elif [ "$SECURITY_SCORE" -ge 60 ]; then
    echo -e "${YELLOW}⚠️  Bien, mais des améliorations sont nécessaires.${NC}" | tee -a "$REPORT_FILE"
else
    echo -e "${RED}🔴 Attention ! Des problèmes critiques doivent être résolus.${NC}" | tee -a "$REPORT_FILE"
fi

echo "" | tee -a "$REPORT_FILE"
echo "📄 Rapport complet sauvegardé dans : $REPORT_FILE"
echo ""
echo "================================================================"
echo "🎯 PROCHAINES ACTIONS RECOMMANDÉES"
echo "================================================================"
echo ""

if [ "$CRITICAL_ISSUES" -gt 0 ]; then
    echo "1. 🔴 Corriger IMMÉDIATEMENT les $CRITICAL_ISSUES problèmes critiques"
fi

if [ "$WARNINGS" -gt 0 ]; then
    echo "2. 🟡 Examiner et corriger les $WARNINGS avertissements"
fi

echo "3. 📖 Lire le rapport détaillé : $REPORT_FILE"
echo "4. 🧪 Tester les corrections dans un environnement de développement"
echo "5. ✅ Relancer l'audit après corrections"
echo ""
echo "================================================================"

exit 0
EOF

