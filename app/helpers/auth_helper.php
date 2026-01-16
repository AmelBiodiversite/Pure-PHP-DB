<?php
/**
 * Fonctions d'aide pour l'authentification et la sécurité
 */

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getCurrentUser() {
    if (!isLoggedIn()) return null;
    $db = \Core\Database::getInstance();
    $stmt = $db->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->execute(['id' => $_SESSION['user_id']]);
    return $stmt->fetch();
}

/**
 * Fonction d'échappement pour éviter les failles XSS
 */
function e($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}
