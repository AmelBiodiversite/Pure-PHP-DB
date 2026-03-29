#!/bin/bash

##############################################################################
# SCRIPT DE BACKUP - Documents Stratégie de Vente MarketFlow Pro
##############################################################################
#
# Ce script crée une sauvegarde complète de tous vos documents de vente
# dans un fichier ZIP horodaté
#
# Usage: ./backup-sales-docs.sh
#
##############################################################################

# Couleurs pour les messages
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${BLUE}╔════════════════════════════════════════════════════════════╗${NC}"
echo -e "${BLUE}║   BACKUP - Documents Stratégie de Vente MarketFlow Pro    ║${NC}"
echo -e "${BLUE}╚════════════════════════════════════════════════════════════╝${NC}"
echo ""

# Configuration
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
TIMESTAMP=$(date +%Y%m%d-%H%M%S)
BACKUP_DIR="$HOME/backups/marketflow-sales"
BACKUP_NAME="marketflow-sales-docs-$TIMESTAMP"
BACKUP_PATH="$BACKUP_DIR/$BACKUP_NAME"

# Créer le dossier de backup s'il n'existe pas
mkdir -p "$BACKUP_DIR"

echo -e "${YELLOW}📁 Création du dossier de backup...${NC}"
mkdir -p "$BACKUP_PATH"

# Liste des fichiers à sauvegarder
FILES=(
    "SALES_PACKAGE_README.md"
    "SALES_INDEX.md"
    "EXECUTIVE_SUMMARY.md"
    "SALES_PLAN.md"
    "SALES_ONE_PAGER.md"
    "COMPETITIVE_ANALYSIS.md"
    "SALES_FAQ.md"
    "GUIDE_SAUVEGARDE.md"
)

# Copier les fichiers
echo -e "${YELLOW}📄 Copie des fichiers...${NC}"
FILE_COUNT=0
for file in "${FILES[@]}"; do
    if [ -f "$SCRIPT_DIR/$file" ]; then
        cp "$SCRIPT_DIR/$file" "$BACKUP_PATH/"
        echo -e "${GREEN}  ✓${NC} $file"
        ((FILE_COUNT++))
    else
        echo -e "${RED}  ✗${NC} $file (non trouvé)"
    fi
done

echo ""
echo -e "${YELLOW}📦 Création de l'archive ZIP...${NC}"

# Créer l'archive ZIP
cd "$BACKUP_DIR"
zip -r "$BACKUP_NAME.zip" "$BACKUP_NAME" > /dev/null 2>&1

if [ $? -eq 0 ]; then
    # Supprimer le dossier temporaire
    rm -rf "$BACKUP_PATH"
    
    # Informations sur le backup
    BACKUP_FILE="$BACKUP_DIR/$BACKUP_NAME.zip"
    BACKUP_SIZE=$(du -h "$BACKUP_FILE" | cut -f1)
    
    echo ""
    echo -e "${GREEN}╔════════════════════════════════════════════════════════════╗${NC}"
    echo -e "${GREEN}║                   ✅ BACKUP RÉUSSI! 🎉                     ║${NC}"
    echo -e "${GREEN}╚════════════════════════════════════════════════════════════╝${NC}"
    echo ""
    echo -e "${BLUE}📊 Statistiques:${NC}"
    echo -e "   • Fichiers sauvegardés: ${GREEN}$FILE_COUNT${NC}"
    echo -e "   • Taille de l'archive:  ${GREEN}$BACKUP_SIZE${NC}"
    echo ""
    echo -e "${BLUE}📍 Emplacement:${NC}"
    echo -e "   ${BACKUP_FILE}"
    echo ""
    echo -e "${BLUE}💡 Pour récupérer:${NC}"
    echo -e "   unzip \"$BACKUP_FILE\" -d ~/restore-location/"
    echo ""
    
    # Liste tous les backups existants
    BACKUP_COUNT=$(ls -1 "$BACKUP_DIR"/*.zip 2>/dev/null | wc -l)
    if [ $BACKUP_COUNT -gt 1 ]; then
        echo -e "${YELLOW}📋 Backups existants ($BACKUP_COUNT):${NC}"
        ls -lh "$BACKUP_DIR"/*.zip | tail -5 | while read line; do
            echo "   $line"
        done
        echo ""
    fi
    
    echo -e "${GREEN}✨ Vos documents sont en sécurité!${NC}"
    echo ""
else
    echo -e "${RED}❌ Erreur lors de la création du backup${NC}"
    exit 1
fi
