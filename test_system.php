<?php
/**
 * =====================================================
 * PAGE DE TEST DU SYSTÈME MARKETFLOW PRO
 * ===================================================== 
 * 
 * Cette page permet de tester :
 * - Le système de notifications toast
 * - Les constantes URL
 * - Le chargement des ressources CSS/JS
 * - Le système de messages flash PHP
 * 
 * URL : https://votre-repl.replit.dev/test_system.php
 * 
 * TRANSMISSION :
 * Cette page sert de référence pour valider que toutes
 * les améliorations apportées au système fonctionnent.
 * ===================================================== 
 */

// Charger la configuration et helpers
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/helpers/functions.php';

// Démarrer la session si nécessaire
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// =====================================================
// GESTION DES TESTS DE NOTIFICATIONS
// =====================================================
if (isset($_GET['test'])) {
    $type = $_GET['test'];
    
    // Messages de test selon le type
    $messages = [
        'success' => '✅ Test réussi ! Le système de notifications fonctionne parfaitement.',
        'error' => '❌ Test d\'erreur : Ce message simule une erreur système.',
        'warning' => '⚠️ Test d\'avertissement : Attention, ceci est un message de test.',
        'info' => 'ℹ️ Test d\'information : Les notifications toast sont opérationnelles !'
    ];
    
    if (isset($messages[$type])) {
        // Utiliser la fonction redirectWithMessage du système
        redirectWithMessage('/test_system.php', $messages[$type], $type);
    }
}

// Variables pour le template
$title = 'Test du Système';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?> - MarketFlow Pro</title>
    
    <!-- =====================================================
         CHARGEMENT DES RESSOURCES AVEC CONSTANTES
         ===================================================== -->
    
    <!-- Styles principaux avec constante CSS_URL -->
    <link rel="stylesheet" href="<?= CSS_URL ?>/style.css">
    
    <!-- Système de notifications toast -->
    <link rel="stylesheet" href="<?= CSS_URL ?>/notifications.css">
    
    <!-- =====================================================
         STYLES SPÉCIFIQUES PAGE DE TEST
         ===================================================== -->
    <style>
        /* Container principal avec gradient moderne */
        body {
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 2rem;
            margin: 0;
        }
        
        /* Card principale */
        .test-container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            padding: 3rem;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        
        /* Titre principal */
        h1 {
            color: #1f2937;
            margin: 0 0 0.5rem 0;
            font-size: 2rem;
            font-weight: 700;
        }
        
        /* Sous-titre */
        .subtitle {
            color: #6b7280;
            margin: 0 0 2rem 0;
            font-size: 1.1rem;
        }
        
        /* Sections */
        .test-section {
            background: #f9fafb;
            padding: 1.5rem;
            border-radius: 12px;
            margin-bottom: 2rem;
        }
        
        .test-section h2 {
            margin: 0 0 1rem 0;
            color: #374151;
            font-size: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        /* Grid de boutons */
        .test-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        
        /* Boutons de test */
        .test-btn {
            padding: 1rem 1.5rem;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        
        .test-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.15);
        }
        
        .test-btn:active {
            transform: translateY(0);
        }
        
        /* Couleurs des boutons selon le type */
        .btn-success { 
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white; 
        }
        .btn-error { 
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white; 
        }
        .btn-warning { 
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white; 
        }
        .btn-info { 
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white; 
        }
        .btn-primary {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            color: white;
        }
        .btn-purple {
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
            color: white;
        }
        .btn-pink {
            background: linear-gradient(135deg, #ec4899 0%, #db2777 100%);
            color: white;
        }
        
        /* Items de status */
        .status-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.875rem 1rem;
            background: white;
            border-radius: 8px;
            margin-bottom: 0.5rem;
            border: 1px solid #e5e7eb;
        }
        
        .status-label {
            font-weight: 500;
            color: #374151;
        }
        
        .status-value {
            font-family: 'Courier New', monospace;
            color: #10b981;
            font-weight: 600;
            font-size: 0.875rem;
        }
        
        /* Séparateur */
        .divider {
            margin: 2rem 0;
            padding-top: 2rem;
            border-top: 2px solid #e5e7eb;
        }
        
        /* Code inline */
        code {
            background: #1f2937;
            color: #10b981;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.875rem;
        }
        
        /* Badge */
        .badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            background: #10b981;
            color: white;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-left: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="test-container">
        <!-- =====================================================
             HEADER DE LA PAGE
             ===================================================== -->
        <h1>🧪 Test du Système MarketFlow Pro</h1>
        <p class="subtitle">Vérification complète des fonctionnalités du système</p>
        
        <!-- =====================================================
             SECTION 1 : CONFIGURATION & CONSTANTES
             ===================================================== -->
        <div class="test-section">
            <h2>
                📊 Configuration & Constantes
                <span class="badge">✓ OK</span>
            </h2>
            
            <div class="status-item">
                <span class="status-label">APP_URL</span>
                <span class="status-value"><?= APP_URL ?></span>
            </div>
            <div class="status-item">
                <span class="status-label">CSS_URL</span>
                <span class="status-value"><?= CSS_URL ?></span>
            </div>
            <div class="status-item">
                <span class="status-label">JS_URL</span>
                <span class="status-value"><?= JS_URL ?></span>
            </div>
            <div class="status-item">
                <span class="status-label">IMG_URL</span>
                <span class="status-value"><?= IMG_URL ?></span>
            </div>
            <div class="status-item">
                <span class="status-label">Environnement</span>
                <span class="status-value"><?= ENVIRONMENT ?></span>
            </div>
        </div>
        
        <!-- =====================================================
             SECTION 2 : TESTS NOTIFICATIONS TOAST (VIA PHP)
             ===================================================== -->
        <div class="test-section">
            <h2>🔔 Notifications Toast (via PHP Flash Messages)</h2>
            <p style="color: #6b7280; margin-bottom: 1rem;">
                Cliquez sur un bouton pour tester une notification avec redirection PHP :
            </p>
            
            <div class="test-grid">
                <a href="?test=success" class="test-btn btn-success">
                    ✅ Succès
                </a>
                <a href="?test=error" class="test-btn btn-error">
                    ❌ Erreur
                </a>
                <a href="?test=warning" class="test-btn btn-warning">
                    ⚠️ Avertissement
                </a>
                <a href="?test=info" class="test-btn btn-info">
                    ℹ️ Information
                </a>
            </div>
        </div>
        
        <!-- =====================================================
             SECTION 3 : TESTS JAVASCRIPT DIRECT
             ===================================================== -->
        <div class="test-section">
            <h2>⚡ Notifications Toast (via JavaScript Direct)</h2>
            <p style="color: #6b7280; margin-bottom: 1rem;">
                Tester les notifications sans rechargement de page via <code>NotificationSystem.show()</code> :
            </p>
            
            <div class="test-grid">
                <button onclick="NotificationSystem.success('✅ Message de succès direct depuis JavaScript !')" class="test-btn btn-success">
                    JS Succès
                </button>
                <button onclick="NotificationSystem.error('❌ Message d\'erreur direct depuis JavaScript !')" class="test-btn btn-error">
                    JS Erreur
                </button>
                <button onclick="NotificationSystem.warning('⚠️ Message d\'avertissement direct depuis JavaScript !')" class="test-btn btn-warning">
                    JS Warning
                </button>
                <button onclick="NotificationSystem.info('ℹ️ Message d\'information direct depuis JavaScript !')" class="test-btn btn-info">
                    JS Info
                </button>
            </div>
        </div>
        
        <!-- =====================================================
             SECTION 4 : TESTS AVANCÉS
             ===================================================== -->
        <div class="test-section">
            <h2>🎯 Tests Avancés</h2>
            <p style="color: #6b7280; margin-bottom: 1rem;">
                Tester des cas d'usage avancés :
            </p>
            
            <div class="test-grid">
                <button onclick="testMultipleNotifications()" class="test-btn btn-primary">
                    🔥 Notifications Multiples
                </button>
                <button onclick="testLongMessage()" class="test-btn btn-purple">
                    📝 Message Long
                </button>
                <button onclick="testCustomDuration()" class="test-btn btn-pink">
                    ⏱️ Durée Personnalisée
                </button>
            </div>
        </div>
        
        <!-- =====================================================
             SECTION 5 : LIENS RAPIDES
             ===================================================== -->
        <div class="divider">
            <h2 style="color: #374151; margin-bottom: 1rem;">🔗 Liens Rapides Système</h2>
            
            <div class="test-grid">
                <a href="/" class="test-btn btn-primary">
                    🏠 Accueil
                </a>
                <a href="/seller/dashboard" class="test-btn btn-purple">
                    📊 Dashboard Vendeur
                </a>
                <a href="/admin" class="test-btn btn-pink">
                    ⚙️ Administration
                </a>
            </div>
        </div>
        
        <!-- =====================================================
             INFORMATIONS TECHNIQUES
             ===================================================== -->
        <div style="margin-top: 2rem; padding: 1rem; background: #f3f4f6; border-radius: 8px; font-size: 0.875rem; color: #6b7280;">
            <strong style="color: #374151;">ℹ️ Informations Techniques :</strong><br>
            • Système de notifications : <code>notifications.js</code> + <code>notifications.css</code><br>
            • Conversion automatique des messages flash PHP en toast<br>
            • Support de 4 types : success, error, warning, info<br>
            • Durée par défaut : 5 secondes (configurable)<br>
            • Position : top-right (modifiable dans config JS)
        </div>
    </div>
    
    <!-- =====================================================
         CHARGEMENT DES SCRIPTS AVEC CONSTANTES
         ===================================================== -->
    
    <!-- Scripts principaux -->
    <script src="<?= JS_URL ?>/app.js"></script>
    <script src="<?= JS_URL ?>/notifications.js"></script>
    
    <!-- =====================================================
         CONVERSION MESSAGE FLASH PHP EN TOAST
         ===================================================== 
         
         Ce bloc est automatiquement traité par notifications.js
         au chargement de la page (DOMContentLoaded)
         ===================================================== -->
    <?php 
        $flash = getFlashMessage();
        if ($flash): 
    ?>
    <div data-flash-message="<?= e($flash['message']) ?>" 
         data-flash-type="<?= $flash['type'] ?>" 
         style="display: none;">
        <!-- Ce message sera converti en toast par notifications.js -->
    </div>
    <?php endif; ?>
    
    <!-- =====================================================
         SCRIPTS DE TEST AVANCÉS
         ===================================================== -->
    <script>
        /**
         * Test : Afficher plusieurs notifications à la suite
         */
        function testMultipleNotifications() {
            NotificationSystem.info('🚀 Démarrage du test...');
            
            setTimeout(() => {
                NotificationSystem.success('✅ Étape 1 terminée !');
            }, 500);
            
            setTimeout(() => {
                NotificationSystem.warning('⚠️ Étape 2 en cours...');
            }, 1000);
            
            setTimeout(() => {
                NotificationSystem.error('❌ Étape 3 simulée (erreur test)');
            }, 1500);
            
            setTimeout(() => {
                NotificationSystem.success('🎉 Test de notifications multiples terminé !');
            }, 2000);
        }
        
        /**
         * Test : Message très long
         */
        function testLongMessage() {
            const longMessage = 'Ceci est un message de test très long pour vérifier que le système de notifications gère correctement les textes étendus. Le système doit afficher le message complet sans déformation et avec un rendu adapté. Les notifications doivent rester lisibles même avec beaucoup de contenu.';
            NotificationSystem.info(longMessage);
        }
        
        /**
         * Test : Durée personnalisée
         */
        function testCustomDuration() {
            NotificationSystem.warning('⏱️ Ce message restera affiché pendant 10 secondes !', 10000);
            NotificationSystem.info('ℹ️ Celui-ci ne durera que 2 secondes.', 2000);
        }
        
        // Log dans la console pour debug
        console.log('🧪 Page de test chargée');
        console.log('📦 NotificationSystem disponible :', typeof NotificationSystem !== 'undefined');
        console.log('⚙️ Configuration :', NotificationSystem?.config);
    </script>
</body>
</html>
