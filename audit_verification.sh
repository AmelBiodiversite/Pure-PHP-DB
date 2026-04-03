#!/bin/bash
# ============================================================
# SCRIPT DE VÉRIFICATION — AUDIT MARKETFLOW PRO
# Date    : 30 mars 2026
# Usage   : bash audit_verification.sh
# Racine  : /var/www/html/Pure-PHP-DB
# ============================================================
# Chaque bloc affiche :
#   [VRAI] → le bug est confirmé (ligne exacte)
#   [ABSENT] → la ligne problématique n'existe pas (bug peut-être déjà corrigé)
# ============================================================

ROOT="/var/www/html/Pure-PHP-DB"
SEP="━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

echo ""
echo "╔══════════════════════════════════════════════════════════╗"
echo "║     VÉRIFICATION AUDIT MARKETFLOW PRO — $(date +%d/%m/%Y)      ║"
echo "╚══════════════════════════════════════════════════════════╝"
echo ""


# ============================================================
# ██████  PROBLÈMES CRITIQUES 🔴
# ============================================================

echo "🔴 ══════════════════════════════════════════════════════"
echo "🔴  PROBLÈMES CRITIQUES"
echo "🔴 ══════════════════════════════════════════════════════"
echo ""


# ------------------------------------------------------------
# [C-01] confirmPayment() absente dans Order.php
# ------------------------------------------------------------
echo "$SEP"
echo "[C-01] Order.php — méthode confirmPayment() inexistante"
echo "$SEP"

FILE="$ROOT/app/models/Order.php"

echo ""
echo "▶ 1/2 — La méthode confirmPayment() DOIT être ABSENTE dans Order.php (bug = elle manque) :"
echo "        → Si la commande suivante ne retourne RIEN : bug confirmé ✅"
grep -n "function confirmPayment" "$FILE" \
  && echo "⚠️  ATTENTION : confirmPayment() existe déjà — bug peut-être déjà corrigé" \
  || echo "✅ BUG CONFIRMÉ : confirmPayment() est introuvable dans Order.php"

echo ""
echo "▶ 2/2 — Vérifier que markAsPaid() existe bien (c'est la méthode à aliaser) :"
grep -n "function markAsPaid" "$FILE" \
  && echo "✅ markAsPaid() est présente — alias possible" \
  || echo "⚠️  markAsPaid() est aussi absente — vérifier le nom exact de la méthode"

echo ""
echo "▶ CONTEXTE — Voir les appels à confirmPayment() dans PaymentController.php :"
grep -n "confirmPayment" "$ROOT/app/controllers/PaymentController.php"

echo ""


# ------------------------------------------------------------
# [C-02] PDO non importé dans PaymentController.php
# ------------------------------------------------------------
echo "$SEP"
echo "[C-02] PaymentController.php — PDO non importé (Fatal Error sur PDO::FETCH_ASSOC)"
echo "$SEP"

FILE="$ROOT/app/controllers/PaymentController.php"

echo ""
echo "▶ 1/2 — Vérifier que 'use PDO;' est ABSENT (bug = il manque) :"
grep -n "^use PDO" "$FILE" \
  && echo "⚠️  'use PDO;' existe déjà — bug peut-être corrigé" \
  || echo "✅ BUG CONFIRMÉ : aucun 'use PDO;' en haut du fichier"

echo ""
echo "▶ 2/2 — Vérifier l'usage de PDO::FETCH_ASSOC (ligne qui crashera) :"
grep -n "PDO::" "$FILE" \
  && echo "✅ PDO:: est utilisé — confirme le bug si 'use PDO' est absent" \
  || echo "ℹ️  Aucun usage de PDO:: détecté dans ce fichier"

echo ""
echo "▶ CONTEXTE — Voir tous les 'use' importés en haut du fichier :"
grep -n "^use " "$FILE"

echo ""


# ------------------------------------------------------------
# [C-03] getOrderDetails() appelé sans $userId
# ------------------------------------------------------------
echo "$SEP"
echo "[C-03] PaymentController.php — getOrderDetails() sans \$userId (faille d'accès)"
echo "$SEP"

FILE_CTRL="$ROOT/app/controllers/PaymentController.php"
FILE_MDL="$ROOT/app/models/Order.php"

echo ""
echo "▶ 1/3 — Signature de getOrderDetails() dans Order.php (nombre de paramètres attendus) :"
grep -n "function getOrderDetails" "$FILE_MDL"

echo ""
echo "▶ 2/3 — Appels à getOrderDetails() dans PaymentController.php :"
grep -n "getOrderDetails" "$FILE_CTRL"

echo ""
echo "▶ 3/3 — Vérifier si \$_SESSION['user_id'] est passé en argument :"
echo "        → Si les lignes ci-dessus ne contiennent PAS 'user_id' : bug confirmé"
grep -n "getOrderDetails.*user_id" "$FILE_CTRL" \
  && echo "⚠️  user_id semble déjà passé — bug peut-être corrigé" \
  || echo "✅ BUG CONFIRMÉ : getOrderDetails() est appelé sans user_id"

echo ""


# ------------------------------------------------------------
# [C-04] Injection SQL dans getRevenueByDay() — $days non casté
# ------------------------------------------------------------
echo "$SEP"
echo "[C-04] Order.php — Injection SQL : \$days non casté en entier"
echo "$SEP"

FILE="$ROOT/app/models/Order.php"

echo ""
echo "▶ 1/3 — Localiser la méthode getRevenueByDay() :"
grep -n "function getRevenueByDay" "$FILE"

echo ""
echo "▶ 2/3 — Vérifier si \$days est injecté directement dans le SQL :"
grep -n "INTERVAL.*days" "$FILE"

echo ""
echo "▶ 3/3 — Vérifier si un cast (int) est présent en début de méthode :"
grep -n "(int).*days\|intval.*days" "$FILE" \
  && echo "⚠️  Cast détecté — bug peut-être corrigé" \
  || echo "✅ BUG CONFIRMÉ : aucun cast (int) \$days trouvé"

echo ""
echo "▶ CONTEXTE — Les 5 lignes autour de l'interpolation SQL :"
grep -n -A2 -B2 "INTERVAL.*days" "$FILE"

echo ""


# ------------------------------------------------------------
# [C-05] Mass-assignment dans createUser() — User.php
# ------------------------------------------------------------
echo "$SEP"
echo "[C-05] User.php — Mass-assignment : pas de whitelist dans createUser()"
echo "$SEP"

FILE="$ROOT/app/models/User.php"

echo ""
echo "▶ 1/3 — Localiser la méthode createUser() :"
grep -n "function createUser" "$FILE"

echo ""
echo "▶ 2/3 — Vérifier si une whitelist est présente :"
grep -n "allowed\|whitelist\|intersect_key\|array_flip" "$FILE" \
  && echo "⚠️  Une whitelist semble présente — bug peut-être corrigé" \
  || echo "✅ BUG CONFIRMÉ : aucune whitelist détectée dans createUser()"

echo ""
echo "▶ 3/3 — Vérifier si array_keys(\$data) est utilisé directement (signe du bug) :"
grep -n "array_keys.*data" "$FILE" \
  && echo "✅ BUG CONFIRMÉ : array_keys(\$data) utilisé directement sans filtrage" \
  || echo "ℹ️  array_keys(\$data) non trouvé — vérifier la logique de construction des champs"

echo ""
echo "▶ CONTEXTE — Les 20 premières lignes de createUser() :"
awk '/function createUser/,/^    \}/' "$FILE" | head -25

echo ""


# ------------------------------------------------------------
# [C-06] Injection SQL sur noms de colonnes dans Model.php
# ------------------------------------------------------------
echo "$SEP"
echo "[C-06] Model.php — Injection SQL : noms de colonnes et ORDER BY non filtrés"
echo "$SEP"

FILE="$ROOT/core/Model.php"

echo ""
echo "▶ 1/4 — Injection dans findAll() via ORDER BY :"
grep -n "ORDER BY.*order\b\|ORDER BY.*\$order" "$FILE" \
  && echo "✅ BUG CONFIRMÉ : ORDER BY construit avec variable non filtrée" \
  || echo "ℹ️  Pattern ORDER BY variable non trouvé — vérifier manuellement"

echo ""
echo "▶ 2/4 — LIMIT injecté sans cast :"
grep -n "LIMIT.*\$limit\b" "$FILE" \
  && echo "✅ BUG CONFIRMÉ : LIMIT sans cast (int)" \
  || echo "ℹ️  LIMIT variable non trouvé — vérifier manuellement"

echo ""
echo "▶ 3/4 — Construction WHERE avec clés de tableau (\$key non filtré) :"
grep -n '"\$key = :\$key"\|"$key = :$key"' "$FILE"
grep -n "\$key.*=.*:\$key\|\$key.*=.*:.*key" "$FILE"

echo ""
echo "▶ 4/4 — Même chose dans update() (\$field non filtré) :"
grep -n "\$field.*=.*:\$field\|fields\[\].*\$field" "$FILE"

echo ""
echo "▶ CONTEXTE — Voir la méthode findAll() complète :"
awk '/function findAll/,/^    \}/' "$FILE"

echo ""


# ------------------------------------------------------------
# [C-07] SSL absent sur la connexion PostgreSQL
# ------------------------------------------------------------
echo "$SEP"
echo "[C-07] Database.php — Connexion PostgreSQL sans SSL (sslmode absent)"
echo "$SEP"

FILE="$ROOT/core/Database.php"

echo ""
echo "▶ 1/2 — Vérifier le DSN PostgreSQL construit :"
grep -n "pgsql:host\|dsn\|DSN" "$FILE"

echo ""
echo "▶ 2/2 — Vérifier si sslmode est présent :"
grep -n "sslmode" "$FILE" \
  && echo "⚠️  sslmode détecté — bug peut-être corrigé" \
  || echo "✅ BUG CONFIRMÉ : sslmode absent du DSN PostgreSQL"

echo ""


# ------------------------------------------------------------
# [C-08] Double confirmation de commande possible
# ------------------------------------------------------------
echo "$SEP"
echo "[C-08] PaymentController.php — Double confirmation de commande possible"
echo "$SEP"

FILE="$ROOT/app/controllers/PaymentController.php"

echo ""
echo "▶ 1/3 — Localiser tous les appels à confirmPayment() (doivent être dans 2 méthodes) :"
grep -n "confirmPayment\|markAsPaid" "$FILE"

echo ""
echo "▶ 2/3 — Vérifier si la méthode success() vérifie le statut AVANT de confirmer :"
grep -n "payment_status.*completed\|completed.*payment_status" "$FILE" \
  && echo "ℹ️  Vérification de statut présente — confirmer si elle précède bien l'appel" \
  || echo "✅ BUG CONFIRMÉ : aucune vérification de statut 'completed' avant re-confirmation"

echo ""
echo "▶ 3/3 — Localiser les méthodes success() et handleCheckoutCompleted() :"
grep -n "function success\|function handleCheckoutCompleted" "$FILE"

echo ""
echo "▶ CONTEXTE — Voir la méthode success() complète :"
awk '/function success\(\)/,/^    \}/' "$FILE"

echo ""


# ============================================================
# 🟠  PROBLÈMES IMPORTANTS
# ============================================================

echo "🟠 ══════════════════════════════════════════════════════"
echo "🟠  PROBLÈMES IMPORTANTS"
echo "🟠 ══════════════════════════════════════════════════════"
echo ""


# ------------------------------------------------------------
# [I-01] CSRF.php — token jamais régénéré après usage
# ------------------------------------------------------------
echo "$SEP"
echo "[I-01] CSRF.php — token jamais invalidé après validation"
echo "$SEP"

FILE="$ROOT/core/CSRF.php"

echo ""
echo "▶ 1/2 — Voir la méthode validateToken() complète :"
awk '/function validateToken/,/^    \}/' "$FILE"

echo ""
echo "▶ 2/2 — Vérifier si unset(\$_SESSION['csrf_token']) est présent après validation :"
grep -n "unset.*csrf_token\|csrf_token.*unset" "$FILE" \
  && echo "⚠️  Token invalidé après usage — bug peut-être corrigé" \
  || echo "✅ BUG CONFIRMÉ : csrf_token jamais supprimé après validation"

echo ""


# ------------------------------------------------------------
# [I-02] AuthController.php — cookie Remember Me sans SameSite
# ------------------------------------------------------------
echo "$SEP"
echo "[I-02] AuthController.php — Cookie 'remember_token' sans SameSite"
echo "$SEP"

FILE="$ROOT/app/controllers/AuthController.php"

echo ""
echo "▶ 1/2 — Voir la ligne setcookie pour remember_token :"
grep -n "setcookie.*remember_token\|remember_token.*setcookie" "$FILE"

echo ""
echo "▶ 2/2 — Vérifier si SameSite est présent :"
grep -n "samesite\|SameSite" "$FILE" \
  && echo "⚠️  SameSite détecté — bug peut-être corrigé" \
  || echo "✅ BUG CONFIRMÉ : SameSite absent du cookie remember_token"

echo ""
echo "▶ CONTEXTE — Les 5 lignes autour du setcookie :"
grep -n -A3 -B1 "setcookie.*remember_token" "$FILE"

echo ""


# ------------------------------------------------------------
# [I-03] AdminController.php — colonne oi.price au lieu de oi.product_price
# ------------------------------------------------------------
echo "$SEP"
echo "[I-03] AdminController.php — colonne 'oi.price' incorrecte (devrait être 'oi.product_price')"
echo "$SEP"

FILE="$ROOT/app/controllers/AdminController.php"

echo ""
echo "▶ 1/2 — Chercher l'usage de oi.price (nom incorrect) :"
grep -n "oi\.price\b" "$FILE" \
  && echo "✅ BUG CONFIRMÉ : 'oi.price' trouvé — devrait être 'oi.product_price'" \
  || echo "⚠️  'oi.price' non trouvé — bug peut-être corrigé ou nom différent"

echo ""
echo "▶ 2/2 — Vérifier si oi.product_price est déjà utilisé :"
grep -n "oi\.product_price" "$FILE" \
  && echo "ℹ️  'oi.product_price' présent — vérifier si les deux coexistent" \
  || echo "ℹ️  'oi.product_price' absent aussi — chercher le bon nom de colonne"

echo ""
echo "▶ CONTEXTE — Voir la requête 'Top vendeurs' dans stats() :"
grep -n -B5 -A5 "total_revenue\|oi\.price\|oi\.product_price" "$FILE" | head -40

echo ""


# ------------------------------------------------------------
# [I-04] AdminController.php — méthode toggleUser() absente mais routée
# ------------------------------------------------------------
echo "$SEP"
echo "[I-04] AdminController.php — toggleUser() absente / routes.php — route définie"
echo "$SEP"

echo ""
echo "▶ 1/2 — Vérifier que la route est bien définie dans routes.php :"
grep -n "toggleUser\|toggle" "$ROOT/config/routes.php" \
  && echo "✅ Route toggle définie dans routes.php" \
  || echo "ℹ️  Aucune route 'toggle' dans routes.php"

echo ""
echo "▶ 2/2 — Vérifier que la méthode toggleUser() est ABSENTE dans AdminController.php :"
grep -n "function toggleUser" "$ROOT/app/controllers/AdminController.php" \
  && echo "⚠️  toggleUser() existe — bug peut-être corrigé" \
  || echo "✅ BUG CONFIRMÉ : toggleUser() introuvable dans AdminController.php"

echo ""


# ------------------------------------------------------------
# [I-05] StripeController.php — pas de vérification CSRF
# ------------------------------------------------------------
echo "$SEP"
echo "[I-05] StripeController.php — createCheckoutSession() sans vérification CSRF"
echo "$SEP"

FILE="$ROOT/app/controllers/StripeController.php"

echo ""
echo "▶ 1/2 — Localiser la méthode createCheckoutSession() :"
grep -n "function createCheckoutSession" "$FILE"

echo ""
echo "▶ 2/2 — Vérifier si une validation CSRF est présente dans cette méthode :"
grep -n "CSRF\|csrf\|validateToken" "$FILE" \
  && echo "⚠️  CSRF trouvé dans le fichier — vérifier s'il est dans createCheckoutSession()" \
  || echo "✅ BUG CONFIRMÉ : aucune vérification CSRF dans StripeController.php"

echo ""
echo "▶ CONTEXTE — Les 15 premières lignes de createCheckoutSession() :"
awk '/function createCheckoutSession/,/^    \}/' "$FILE" | head -20

echo ""


# ------------------------------------------------------------
# [I-06] AdminController.php — protection admin par email codé en dur
# ------------------------------------------------------------
echo "$SEP"
echo "[I-06] AdminController.php — protection admin basée sur email hardcodé"
echo "$SEP"

FILE="$ROOT/app/controllers/AdminController.php"

echo ""
echo "▶ 1/2 — Chercher un email codé en dur (admin@...) :"
grep -n "admin@\|@marketflow" "$FILE" \
  && echo "✅ BUG CONFIRMÉ : email hardcodé trouvé dans AdminController" \
  || echo "ℹ️  Aucun email hardcodé trouvé — vérifier le mécanisme de protection"

echo ""
echo "▶ 2/2 — Vérifier si la protection est basée sur le rôle en BDD :"
grep -n "role.*admin\|admin.*role" "$FILE" \
  && echo "ℹ️  Référence au rôle 'admin' trouvée — vérifier si elle remplace l'email" \
  || echo "ℹ️  Aucune vérification par rôle — tout repose probablement sur l'email"

echo ""
echo "▶ CONTEXTE — Les lignes autour de l'email/rôle admin :"
grep -n -B3 -A3 "admin@\|is_super_admin\|role.*admin" "$FILE" | head -30

echo ""


# ------------------------------------------------------------
# [I-07] User.php — IP non filtrée derrière proxy Railway
# ------------------------------------------------------------
echo "$SEP"
echo "[I-07] User.php — REMOTE_ADDR utilisé derrière proxy (IP proxy enregistrée)"
echo "$SEP"

FILE="$ROOT/app/models/User.php"

echo ""
echo "▶ 1/2 — Chercher l'usage de REMOTE_ADDR :"
grep -n "REMOTE_ADDR" "$FILE" \
  && echo "✅ BUG CONFIRMÉ : REMOTE_ADDR utilisé directement" \
  || echo "ℹ️  REMOTE_ADDR non trouvé dans User.php"

echo ""
echo "▶ 2/2 — Vérifier si HTTP_X_FORWARDED_FOR est utilisé (fix attendu) :"
grep -n "HTTP_X_FORWARDED_FOR\|X_FORWARDED" "$FILE" \
  && echo "⚠️  X_FORWARDED_FOR trouvé — bug peut-être corrigé" \
  || echo "✅ BUG CONFIRMÉ : HTTP_X_FORWARDED_FOR absent — IP proxy enregistrée"

echo ""
echo "▶ CONTEXTE — Lignes autour de ip_address / REMOTE_ADDR :"
grep -n -B2 -A2 "ip_address\|REMOTE_ADDR" "$FILE"

echo ""


# ------------------------------------------------------------
# [I-08] Order.php — récursion infinie dans generateLicenseKey()
# ------------------------------------------------------------
echo "$SEP"
echo "[I-08] Order.php — récursion infinie dans generateLicenseKey()"
echo "$SEP"

FILE="$ROOT/app/models/Order.php"

echo ""
echo "▶ 1/2 — Localiser la méthode generateLicenseKey() :"
grep -n "function generateLicenseKey" "$FILE"

echo ""
echo "▶ 2/2 — Vérifier si la récursion est présente ET sans compteur de tentatives :"
grep -n "generateLicenseKey\|attempt\|\$attempt" "$FILE"
echo ""
echo "        → Si generateLicenseKey() s'appelle elle-même SANS paramètre 'attempt' : bug confirmé"
grep -n "return.*generateLicenseKey()" "$FILE" \
  && echo "✅ BUG CONFIRMÉ : récursion sans limite détectée" \
  || echo "ℹ️  Récursion directe non trouvée — vérifier la logique"

echo ""
echo "▶ CONTEXTE — Voir la méthode complète :"
awk '/function generateLicenseKey/,/^    \}/' "$FILE"

echo ""


# ============================================================
# 🟡  PROBLÈMES MOYENS
# ============================================================

echo "🟡 ══════════════════════════════════════════════════════"
echo "🟡  PROBLÈMES MOYENS"
echo "🟡 ══════════════════════════════════════════════════════"
echo ""


# ------------------------------------------------------------
# [M-01] config.php — .env chargé trop tard
# ------------------------------------------------------------
echo "$SEP"
echo "[M-01] config.php — lecture .env APRÈS les define() (variables non disponibles)"
echo "$SEP"

FILE="$ROOT/config/config.php"

echo ""
echo "▶ 1/2 — Voir les 20 premières lignes du fichier (ordre de chargement) :"
head -20 "$FILE"

echo ""
echo "▶ 2/2 — Chercher où le .env est lu/parsé :"
grep -n "\.env\|parse_ini\|file_get_contents.*env\|getenv\|Env::" "$FILE" | head -10

echo ""
echo "▶ CONTEXTE — Voir le premier define() et la première lecture .env :"
echo "  Premier define() :"
grep -n "^define(" "$FILE" | head -3
echo "  Chargement .env :"
grep -n "\.env\|Env::\|parse_ini" "$FILE" | head -3

echo ""


# ------------------------------------------------------------
# [M-02] session.php — détection production incohérente
# ------------------------------------------------------------
echo "$SEP"
echo "[M-02] session.php vs config.php — détection production différente"
echo "$SEP"

echo ""
echo "▶ 1/2 — Méthode de détection dans session.php :"
grep -n "isProduction\|SERVER_NAME\|localhost\|RAILWAY" "$ROOT/config/session.php"

echo ""
echo "▶ 2/2 — Méthode de détection dans config.php :"
grep -n "isProduction\|RAILWAY_ENVIRONMENT\|localhost" "$ROOT/config/config.php"

echo ""
echo "        → Si les deux méthodes sont différentes : bug confirmé"

echo ""


# ------------------------------------------------------------
# [M-03] Router.php — message debug visible en production
# ------------------------------------------------------------
echo "$SEP"
echo "[M-03] Router.php — message debug affiché en production"
echo "$SEP"

FILE="$ROOT/core/Router.php"

echo ""
echo "▶ 1/2 — Chercher l'affichage du message debug :"
grep -n "debug\|Debug\|🐛" "$FILE" \
  && echo "✅ BUG CONFIRMÉ : message debug trouvé dans Router.php" \
  || echo "ℹ️  Aucun message debug trouvé"

echo ""
echo "▶ 2/2 — Vérifier si l'affichage est conditionné à l'environnement :"
grep -n "isProduction\|RAILWAY_ENVIRONMENT\|getenv" "$FILE" \
  && echo "⚠️  Condition d'environnement présente — bug peut-être corrigé" \
  || echo "✅ BUG CONFIRMÉ : debug affiché sans vérifier l'environnement"

echo ""
echo "▶ CONTEXTE — Voir les lignes autour du message debug :"
grep -n -B3 -A3 "debug\|Debug\|🐛" "$FILE"

echo ""


# ------------------------------------------------------------
# [M-04] AuthController.php — mot de passe minimum 6 caractères
# ------------------------------------------------------------
echo "$SEP"
echo "[M-04] AuthController.php — mot de passe minimum trop faible (6 chars)"
echo "$SEP"

FILE="$ROOT/app/controllers/AuthController.php"

echo ""
echo "▶ 1/1 — Chercher la validation de longueur du mot de passe :"
grep -n "strlen.*password\|password.*strlen\|< 6\|< 7\|< 8" "$FILE" \
  && echo "✅ Validation trouvée — vérifier la valeur du minimum" \
  || echo "ℹ️  Aucune validation de longueur trouvée"

echo ""
echo "▶ CONTEXTE — Lignes autour de la validation :"
grep -n -B2 -A2 "strlen.*password\|password.*strlen" "$FILE"

echo ""


# ------------------------------------------------------------
# [M-05] Database.php — __wakeup() déclaré public
# ------------------------------------------------------------
echo "$SEP"
echo "[M-05] Database.php — __wakeup() devrait être private"
echo "$SEP"

FILE="$ROOT/core/Database.php"

echo ""
echo "▶ 1/1 — Vérifier la visibilité de __wakeup() :"
grep -n "__wakeup\|__clone" "$FILE"
echo ""
echo "        → Si la ligne contient 'public function __wakeup' : bug confirmé"
grep -n "public function __wakeup" "$FILE" \
  && echo "✅ BUG CONFIRMÉ : __wakeup() est public" \
  || echo "⚠️  __wakeup() n'est pas public — vérifier la visibilité exacte"

echo ""


# ------------------------------------------------------------
# [M-06] security_headers.php — unsafe-inline dans script-src
# ------------------------------------------------------------
echo "$SEP"
echo "[M-06] security_headers.php — 'unsafe-inline' affaiblit le CSP"
echo "$SEP"

FILE="$ROOT/config/security_headers.php"

echo ""
echo "▶ 1/2 — Chercher unsafe-inline dans les headers :"
grep -n "unsafe-inline" "$FILE" \
  && echo "✅ BUG CONFIRMÉ : 'unsafe-inline' trouvé dans les headers CSP" \
  || echo "⚠️  'unsafe-inline' absent — bug peut-être corrigé"

echo ""
echo "▶ 2/2 — Voir la directive Content-Security-Policy complète :"
grep -n "Content-Security-Policy\|script-src\|style-src" "$FILE"

echo ""


# ============================================================
# RÉCAPITULATIF FINAL
# ============================================================

echo ""
echo "╔══════════════════════════════════════════════════════════╗"
echo "║                  VÉRIFICATION TERMINÉE                   ║"
echo "╚══════════════════════════════════════════════════════════╝"
echo ""
echo " Légende des résultats :"
echo "   ✅ BUG CONFIRMÉ    → le bug existe, correction nécessaire"
echo "   ⚠️  PEUT-ÊTRE CORRIGÉ → vérifier manuellement"
echo "   ℹ️  INFO            → information complémentaire"
echo ""
echo " Fichiers vérifiés :"
echo "   $ROOT/app/models/Order.php"
echo "   $ROOT/app/models/User.php"
echo "   $ROOT/app/controllers/PaymentController.php"
echo "   $ROOT/app/controllers/AuthController.php"
echo "   $ROOT/app/controllers/AdminController.php"
echo "   $ROOT/app/controllers/StripeController.php"
echo "   $ROOT/core/Database.php"
echo "   $ROOT/core/Model.php"
echo "   $ROOT/core/CSRF.php"
echo "   $ROOT/core/Router.php"
echo "   $ROOT/config/config.php"
echo "   $ROOT/config/session.php"
echo "   $ROOT/config/security_headers.php"
echo "   $ROOT/config/routes.php"
echo ""

