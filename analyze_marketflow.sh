#!/bin/bash

# ================================================================
# MARKETFLOW PRO - ANALYSE APPROFONDIE DU CODE
# ================================================================
# Ce script analyse en profondeur l'architecture, les patterns,
# et la logique métier de MarketFlow Pro pour mieux comprendre
# le code et fournir une aide plus précise.
# ================================================================

PROJECT_ROOT="/home/runner/workspace"
OUTPUT_FILE="marketflow_analysis_$(date +%Y%m%d_%H%M%S).txt"

echo "================================================================"
echo "🔍 ANALYSE APPROFONDIE MARKETFLOW PRO"
echo "================================================================"
echo ""
echo "📁 Projet : $PROJECT_ROOT"
echo "📄 Rapport : $OUTPUT_FILE"
echo ""

# Initialiser le rapport
cat > "$OUTPUT_FILE" << 'EOF'
================================================================
🔍 ANALYSE APPROFONDIE MARKETFLOW PRO
================================================================

EOF

echo "⏳ Analyse en cours..."

# ================================================================
# 1. STRUCTURE DU PROJET
# ================================================================
echo "" >> "$OUTPUT_FILE"
echo "================================================================" >> "$OUTPUT_FILE"
echo "📁 1. STRUCTURE DU PROJET" >> "$OUTPUT_FILE"
echo "================================================================" >> "$OUTPUT_FILE"
echo "" >> "$OUTPUT_FILE"

echo "📦 Arborescence complète :" >> "$OUTPUT_FILE"
tree -L 3 -I 'node_modules|vendor|.git' "$PROJECT_ROOT" 2>/dev/null >> "$OUTPUT_FILE" || \
    find "$PROJECT_ROOT" -maxdepth 3 -type d | grep -v 'node_modules\|vendor\|\.git' | sort >> "$OUTPUT_FILE"

echo "" >> "$OUTPUT_FILE"
echo "📊 Statistiques par dossier :" >> "$OUTPUT_FILE"
for dir in app public config database logs uploads; do
    if [ -d "$PROJECT_ROOT/$dir" ]; then
        echo "" >> "$OUTPUT_FILE"
        echo "📂 $dir/ :" >> "$OUTPUT_FILE"
        find "$PROJECT_ROOT/$dir" -type f 2>/dev/null | wc -l | xargs echo "  - Fichiers :" >> "$OUTPUT_FILE"
        du -sh "$PROJECT_ROOT/$dir" 2>/dev/null | awk '{print "  - Taille : " $1}' >> "$OUTPUT_FILE"
    fi
done

# ================================================================
# 2. ARCHITECTURE MVC DÉTAILLÉE
# ================================================================
echo "" >> "$OUTPUT_FILE"
echo "================================================================" >> "$OUTPUT_FILE"
echo "🏗️ 2. ARCHITECTURE MVC" >> "$OUTPUT_FILE"
echo "================================================================" >> "$OUTPUT_FILE"
echo "" >> "$OUTPUT_FILE"

echo "📋 CONTRÔLEURS (app/controllers/) :" >> "$OUTPUT_FILE"
if [ -d "$PROJECT_ROOT/app/controllers" ]; then
    for controller in "$PROJECT_ROOT/app/controllers"/*.php; do
        if [ -f "$controller" ]; then
            filename=$(basename "$controller")
            echo "" >> "$OUTPUT_FILE"
            echo "  📄 $filename" >> "$OUTPUT_FILE"
            
            # Nombre de lignes
            lines=$(wc -l < "$controller")
            echo "    - Lignes : $lines" >> "$OUTPUT_FILE"
            
            # Méthodes publiques
            echo "    - Méthodes publiques :" >> "$OUTPUT_FILE"
            grep -n "public function" "$controller" | while read -r line; do
                method=$(echo "$line" | sed 's/.*public function \([^(]*\).*/\1/')
                linenum=$(echo "$line" | cut -d: -f1)
                echo "      → $method() [ligne $linenum]" >> "$OUTPUT_FILE"
            done
            
            # Dépendances (use statements)
            deps=$(grep "^use " "$controller" | wc -l)
            if [ "$deps" -gt 0 ]; then
                echo "    - Dépendances : $deps" >> "$OUTPUT_FILE"
            fi
        fi
    done
fi

echo "" >> "$OUTPUT_FILE"
echo "📋 MODÈLES (app/models/) :" >> "$OUTPUT_FILE"
if [ -d "$PROJECT_ROOT/app/models" ]; then
    for model in "$PROJECT_ROOT/app/models"/*.php; do
        if [ -f "$model" ]; then
            filename=$(basename "$model")
            echo "" >> "$OUTPUT_FILE"
            echo "  📄 $filename" >> "$OUTPUT_FILE"
            
            lines=$(wc -l < "$model")
            echo "    - Lignes : $lines" >> "$OUTPUT_FILE"
            
            # Propriétés de la classe
            echo "    - Propriétés :" >> "$OUTPUT_FILE"
            grep -E "(private|protected|public) \\\$" "$model" | head -10 | while read -r prop; do
                echo "      → $(echo $prop | sed 's/^\s*//')" >> "$OUTPUT_FILE"
            done
            
            # Méthodes
            echo "    - Méthodes publiques :" >> "$OUTPUT_FILE"
            grep -n "public function" "$model" | head -10 | while read -r line; do
                method=$(echo "$line" | sed 's/.*public function \([^(]*\).*/\1/')
                linenum=$(echo "$line" | cut -d: -f1)
                echo "      → $method() [ligne $linenum]" >> "$OUTPUT_FILE"
            done
        fi
    done
fi

echo "" >> "$OUTPUT_FILE"
echo "📋 VUES (app/views/) :" >> "$OUTPUT_FILE"
if [ -d "$PROJECT_ROOT/app/views" ]; then
    echo "  Nombre total de fichiers vues :" >> "$OUTPUT_FILE"
    find "$PROJECT_ROOT/app/views" -name "*.php" | wc -l | xargs echo "    →" >> "$OUTPUT_FILE"
    
    echo "" >> "$OUTPUT_FILE"
    echo "  Organisation par dossier :" >> "$OUTPUT_FILE"
    for viewdir in "$PROJECT_ROOT/app/views"/*; do
        if [ -d "$viewdir" ]; then
            dirname=$(basename "$viewdir")
            count=$(find "$viewdir" -name "*.php" | wc -l)
            echo "    → $dirname/ : $count fichiers" >> "$OUTPUT_FILE"
        fi
    done
fi

# ================================================================
# 3. BASE DE DONNÉES
# ================================================================
echo "" >> "$OUTPUT_FILE"
echo "================================================================" >> "$OUTPUT_FILE"
echo "🗄️ 3. SCHÉMA DE BASE DE DONNÉES" >> "$OUTPUT_FILE"
echo "================================================================" >> "$OUTPUT_FILE"
echo "" >> "$OUTPUT_FILE"

echo "📊 Analyse des migrations SQL :" >> "$OUTPUT_FILE"
if [ -d "$PROJECT_ROOT/database" ]; then
    for sqlfile in "$PROJECT_ROOT/database"/*.sql; do
        if [ -f "$sqlfile" ]; then
            filename=$(basename "$sqlfile")
            echo "" >> "$OUTPUT_FILE"
            echo "  📄 $filename" >> "$OUTPUT_FILE"
            
            # Tables créées
            echo "    Tables créées :" >> "$OUTPUT_FILE"
            grep -i "CREATE TABLE" "$sqlfile" | sed 's/CREATE TABLE IF NOT EXISTS //' | sed 's/CREATE TABLE //' | sed 's/ (.*$//' | sed 's/`//g' | while read -r table; do
                echo "      → $table" >> "$OUTPUT_FILE"
                
                # Colonnes de cette table
                awk "/CREATE TABLE.*$table/,/\);/" "$sqlfile" | grep -E "^\s+\w+" | grep -v "PRIMARY KEY\|FOREIGN KEY\|CONSTRAINT" | head -5 | while read -r col; do
                    echo "        - $(echo $col | sed 's/,$//')" >> "$OUTPUT_FILE"
                done
            done
            
            # Relations (FOREIGN KEY)
            fk_count=$(grep -i "FOREIGN KEY" "$sqlfile" | wc -l)
            if [ "$fk_count" -gt 0 ]; then
                echo "    Relations (FOREIGN KEY) : $fk_count" >> "$OUTPUT_FILE"
            fi
        fi
    done
fi

echo "" >> "$OUTPUT_FILE"
echo "📋 Tables identifiées dans le code :" >> "$OUTPUT_FILE"
grep -rh "FROM \|INSERT INTO \|UPDATE " "$PROJECT_ROOT/app" --include="*.php" 2>/dev/null | \
    grep -oE "(FROM|INTO|UPDATE) [a-z_]+" | \
    awk '{print $2}' | sort -u | while read -r table; do
    echo "  → $table" >> "$OUTPUT_FILE"
done

# ================================================================
# 4. ROUTES & ENDPOINTS
# ================================================================
echo "" >> "$OUTPUT_FILE"
echo "================================================================" >> "$OUTPUT_FILE"
echo "🛣️ 4. ROUTES & ENDPOINTS" >> "$OUTPUT_FILE"
echo "================================================================" >> "$OUTPUT_FILE"
echo "" >> "$OUTPUT_FILE"

if [ -f "$PROJECT_ROOT/public/index.php" ]; then
    echo "📄 Analyse de public/index.php :" >> "$OUTPUT_FILE"
    echo "" >> "$OUTPUT_FILE"
    
    # Routes détectées
    grep -n "case '\|Route::" "$PROJECT_ROOT/public/index.php" | head -30 | while read -r line; do
        echo "  $(echo $line | sed 's/^\s*//')" >> "$OUTPUT_FILE"
    done
fi

echo "" >> "$OUTPUT_FILE"
echo "📋 Endpoints API détectés :" >> "$OUTPUT_FILE"
grep -rn "application/json\|header('Content-Type: application/json" "$PROJECT_ROOT/app" --include="*.php" 2>/dev/null | \
    cut -d: -f1 | sort -u | while read -r file; do
    filename=$(basename "$file")
    echo "  → $filename" >> "$OUTPUT_FILE"
done

# ================================================================
# 5. INTÉGRATIONS EXTERNES
# ================================================================
echo "" >> "$OUTPUT_FILE"
echo "================================================================" >> "$OUTPUT_FILE"
echo "🔌 5. INTÉGRATIONS EXTERNES" >> "$OUTPUT_FILE"
echo "================================================================" >> "$OUTPUT_FILE"
echo "" >> "$OUTPUT_FILE"

echo "💳 STRIPE :" >> "$OUTPUT_FILE"
stripe_files=$(grep -rl "Stripe\|stripe" "$PROJECT_ROOT/app" --include="*.php" 2>/dev/null | wc -l)
echo "  - Fichiers utilisant Stripe : $stripe_files" >> "$OUTPUT_FILE"

if [ -f "$PROJECT_ROOT/app/controllers/StripeController.php" ]; then
    echo "  - Endpoints Stripe :" >> "$OUTPUT_FILE"
    grep -n "public function" "$PROJECT_ROOT/app/controllers/StripeController.php" | while read -r line; do
        method=$(echo "$line" | sed 's/.*public function \([^(]*\).*/\1/')
        echo "    → $method()" >> "$OUTPUT_FILE"
    done
fi

echo "" >> "$OUTPUT_FILE"
echo "📧 EMAIL :" >> "$OUTPUT_FILE"
email_count=$(grep -r "mail(\|PHPMailer\|sendmail" "$PROJECT_ROOT/app" --include="*.php" 2>/dev/null | wc -l)
echo "  - Références email dans le code : $email_count" >> "$OUTPUT_FILE"

echo "" >> "$OUTPUT_FILE"
echo "📦 COMPOSER :" >> "$OUTPUT_FILE"
if [ -f "$PROJECT_ROOT/composer.json" ]; then
    echo "  - Dépendances :" >> "$OUTPUT_FILE"
    grep -A 20 '"require"' "$PROJECT_ROOT/composer.json" | grep '"' | grep -v 'require' | while read -r dep; do
        echo "    $(echo $dep | sed 's/^\s*//')" >> "$OUTPUT_FILE"
    done
fi

# ================================================================
# 6. CONFIGURATION
# ================================================================
echo "" >> "$OUTPUT_FILE"
echo "================================================================" >> "$OUTPUT_FILE"
echo "⚙️ 6. CONFIGURATION" >> "$OUTPUT_FILE"
echo "================================================================" >> "$OUTPUT_FILE"
echo "" >> "$OUTPUT_FILE"

if [ -f "$PROJECT_ROOT/config/config.php" ]; then
    echo "📄 config/config.php :" >> "$OUTPUT_FILE"
    echo "" >> "$OUTPUT_FILE"
    
    # Constants définies
    echo "  Constantes définies :" >> "$OUTPUT_FILE"
    grep "define(" "$PROJECT_ROOT/config/config.php" | head -20 | while read -r line; do
        echo "    $(echo $line | sed 's/^\s*//')" >> "$OUTPUT_FILE"
    done
fi

if [ -f "$PROJECT_ROOT/.env" ]; then
    echo "" >> "$OUTPUT_FILE"
    echo "🔒 .env détecté :" >> "$OUTPUT_FILE"
    echo "  - Variables d'environnement configurées" >> "$OUTPUT_FILE"
    grep "^[A-Z]" "$PROJECT_ROOT/.env" 2>/dev/null | cut -d= -f1 | while read -r var; do
        echo "    → $var" >> "$OUTPUT_FILE"
    done
fi

# ================================================================
# 7. PATTERNS & PRATIQUES
# ================================================================
echo "" >> "$OUTPUT_FILE"
echo "================================================================" >> "$OUTPUT_FILE"
echo "💎 7. PATTERNS & BONNES PRATIQUES" >> "$OUTPUT_FILE"
echo "================================================================" >> "$OUTPUT_FILE"
echo "" >> "$OUTPUT_FILE"

echo "🔒 SÉCURITÉ :" >> "$OUTPUT_FILE"
prepared=$(grep -r "prepare(\|execute(" "$PROJECT_ROOT/app" --include="*.php" 2>/dev/null | wc -l)
echo "  - Requêtes préparées (PDO) : $prepared occurrences" >> "$OUTPUT_FILE"

xss=$(grep -r "htmlspecialchars\|htmlentities" "$PROJECT_ROOT/app" --include="*.php" 2>/dev/null | wc -l)
echo "  - Protection XSS : $xss occurrences" >> "$OUTPUT_FILE"

csrf=$(grep -r "csrf_token\|CSRF" "$PROJECT_ROOT/app" --include="*.php" 2>/dev/null | wc -l)
echo "  - Protection CSRF : $csrf occurrences" >> "$OUTPUT_FILE"

password=$(grep -r "password_hash\|password_verify" "$PROJECT_ROOT/app" --include="*.php" 2>/dev/null | wc -l)
echo "  - Hachage mots de passe : $password occurrences" >> "$OUTPUT_FILE"

echo "" >> "$OUTPUT_FILE"
echo "🏗️ ARCHITECTURE :" >> "$OUTPUT_FILE"
classes=$(grep -r "^class " "$PROJECT_ROOT/app" --include="*.php" 2>/dev/null | wc -l)
echo "  - Classes définies : $classes" >> "$OUTPUT_FILE"

interfaces=$(grep -r "^interface " "$PROJECT_ROOT/app" --include="*.php" 2>/dev/null | wc -l)
echo "  - Interfaces : $interfaces" >> "$OUTPUT_FILE"

traits=$(grep -r "^trait " "$PROJECT_ROOT/app" --include="*.php" 2>/dev/null | wc -l)
echo "  - Traits : $traits" >> "$OUTPUT_FILE"

echo "" >> "$OUTPUT_FILE"
echo "📝 DOCUMENTATION :" >> "$OUTPUT_FILE"
comments=$(grep -r "/\*\*\|///" "$PROJECT_ROOT/app" --include="*.php" 2>/dev/null | wc -l)
echo "  - Commentaires doc : $comments" >> "$OUTPUT_FILE"

todos=$(grep -r "TODO\|FIXME\|XXX" "$PROJECT_ROOT/app" --include="*.php" 2>/dev/null | wc -l)
echo "  - TODO/FIXME : $todos" >> "$OUTPUT_FILE"

# ================================================================
# 8. FONCTIONNALITÉS MÉTIER
# ================================================================
echo "" >> "$OUTPUT_FILE"
echo "================================================================" >> "$OUTPUT_FILE"
echo "🎯 8. FONCTIONNALITÉS MÉTIER" >> "$OUTPUT_FILE"
echo "================================================================" >> "$OUTPUT_FILE"
echo "" >> "$OUTPUT_FILE"

echo "📦 PRODUITS :" >> "$OUTPUT_FILE"
if [ -f "$PROJECT_ROOT/app/controllers/ProductController.php" ]; then
    echo "  Méthodes ProductController :" >> "$OUTPUT_FILE"
    grep "public function" "$PROJECT_ROOT/app/controllers/ProductController.php" | while read -r line; do
        method=$(echo "$line" | sed 's/.*public function \([^(]*\).*/\1/')
        echo "    → $method()" >> "$OUTPUT_FILE"
    done
fi

echo "" >> "$OUTPUT_FILE"
echo "🛒 PANIER & COMMANDES :" >> "$OUTPUT_FILE"
cart_files=$(find "$PROJECT_ROOT/app" -name "*Cart*" -o -name "*Order*" 2>/dev/null)
if [ -n "$cart_files" ]; then
    echo "$cart_files" | while read -r file; do
        echo "  → $(basename $file)" >> "$OUTPUT_FILE"
    done
fi

echo "" >> "$OUTPUT_FILE"
echo "👤 UTILISATEURS :" >> "$OUTPUT_FILE"
if [ -f "$PROJECT_ROOT/app/controllers/AuthController.php" ]; then
    echo "  Méthodes AuthController :" >> "$OUTPUT_FILE"
    grep "public function" "$PROJECT_ROOT/app/controllers/AuthController.php" | while read -r line; do
        method=$(echo "$line" | sed 's/.*public function \([^(]*\).*/\1/')
        echo "    → $method()" >> "$OUTPUT_FILE"
    done
fi

echo "" >> "$OUTPUT_FILE"
echo "⭐ REVIEWS :" >> "$OUTPUT_FILE"
if [ -f "$PROJECT_ROOT/app/controllers/ReviewController.php" ]; then
    echo "  ReviewController détecté" >> "$OUTPUT_FILE"
fi

echo "" >> "$OUTPUT_FILE"
echo "❤️ WISHLIST :" >> "$OUTPUT_FILE"
wishlist_count=$(grep -r "wishlist" "$PROJECT_ROOT/app" --include="*.php" 2>/dev/null | wc -l)
echo "  - Références wishlist : $wishlist_count" >> "$OUTPUT_FILE"

# ================================================================
# 9. ASSETS & FRONTEND
# ================================================================
echo "" >> "$OUTPUT_FILE"
echo "================================================================" >> "$OUTPUT_FILE"
echo "🎨 9. ASSETS & FRONTEND" >> "$OUTPUT_FILE"
echo "================================================================" >> "$OUTPUT_FILE"
echo "" >> "$OUTPUT_FILE"

if [ -d "$PROJECT_ROOT/public" ]; then
    echo "📊 Fichiers public/ :" >> "$OUTPUT_FILE"
    
    js_count=$(find "$PROJECT_ROOT/public" -name "*.js" 2>/dev/null | wc -l)
    echo "  - JavaScript : $js_count fichiers" >> "$OUTPUT_FILE"
    
    css_count=$(find "$PROJECT_ROOT/public" -name "*.css" 2>/dev/null | wc -l)
    echo "  - CSS : $css_count fichiers" >> "$OUTPUT_FILE"
    
    img_count=$(find "$PROJECT_ROOT/public" -type f \( -name "*.jpg" -o -name "*.png" -o -name "*.svg" -o -name "*.gif" \) 2>/dev/null | wc -l)
    echo "  - Images : $img_count fichiers" >> "$OUTPUT_FILE"
fi

echo "" >> "$OUTPUT_FILE"
tailwind=$(grep -r "tailwind\|@apply" "$PROJECT_ROOT" --include="*.css" --include="*.html" --include="*.php" 2>/dev/null | wc -l)
if [ "$tailwind" -gt 0 ]; then
    echo "🎨 Tailwind CSS détecté : $tailwind références" >> "$OUTPUT_FILE"
fi

chartjs=$(grep -r "Chart.js\|new Chart(" "$PROJECT_ROOT" --include="*.js" --include="*.php" 2>/dev/null | wc -l)
if [ "$chartjs" -gt 0 ]; then
    echo "📊 Chart.js détecté : $chartjs références" >> "$OUTPUT_FILE"
fi

# ================================================================
# 10. RÉSUMÉ STATISTIQUES GLOBALES
# ================================================================
echo "" >> "$OUTPUT_FILE"
echo "================================================================" >> "$OUTPUT_FILE"
echo "📊 10. STATISTIQUES GLOBALES" >> "$OUTPUT_FILE"
echo "================================================================" >> "$OUTPUT_FILE"
echo "" >> "$OUTPUT_FILE"

total_php=$(find "$PROJECT_ROOT/app" -name "*.php" 2>/dev/null | wc -l)
echo "📄 Fichiers PHP : $total_php" >> "$OUTPUT_FILE"

total_lines=$(find "$PROJECT_ROOT/app" -name "*.php" -exec wc -l {} + 2>/dev/null | tail -1 | awk '{print $1}')
echo "📝 Lignes de code PHP : $total_lines" >> "$OUTPUT_FILE"

total_size=$(du -sh "$PROJECT_ROOT" 2>/dev/null | awk '{print $1}')
echo "💾 Taille totale projet : $total_size" >> "$OUTPUT_FILE"

echo "" >> "$OUTPUT_FILE"
echo "🎯 Score de complexité estimé :" >> "$OUTPUT_FILE"
complexity=$((total_php * 10))
echo "  → $complexity points (basé sur nombre de fichiers)" >> "$OUTPUT_FILE"

# ================================================================
# FIN DU RAPPORT
# ================================================================
echo "" >> "$OUTPUT_FILE"
echo "================================================================" >> "$OUTPUT_FILE"
echo "✅ ANALYSE TERMINÉE" >> "$OUTPUT_FILE"
echo "================================================================" >> "$OUTPUT_FILE"
echo "" >> "$OUTPUT_FILE"
echo "📄 Rapport complet généré : $OUTPUT_FILE" >> "$OUTPUT_FILE"
echo "⏰ Date : $(date '+%Y-%m-%d %H:%M:%S')" >> "$OUTPUT_FILE"
echo "" >> "$OUTPUT_FILE"

# Afficher le résumé à l'écran
echo ""
echo "✅ Analyse terminée !"
echo ""
echo "📄 Rapport complet : $OUTPUT_FILE"
echo ""
echo "📊 Résumé rapide :"
echo "  - Fichiers PHP : $total_php"
echo "  - Lignes de code : $total_lines"
echo "  - Taille projet : $total_size"
echo ""
echo "🔍 Pour voir le rapport complet :"
echo "  cat $OUTPUT_FILE"
echo ""
echo "💡 Pour une recherche spécifique dans le rapport :"
echo "  grep 'mot-clé' $OUTPUT_FILE"
echo ""

