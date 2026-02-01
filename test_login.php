<?php
require_once __DIR__ . '/core/Database.php';
require_once __DIR__ . '/core/Env.php';

use Core\Database;

$db = Database::getInstance();

$email = 'admin@marketflow.com';
$password = 'admin123';

$sql = "SELECT * FROM users WHERE (email ILIKE :login OR username ILIKE :login) AND is_active = TRUE LIMIT 1";
$stmt = $db->prepare($sql);
$stmt->execute(['login' => $email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

echo "=== TEST DE CONNEXION ===\n";
echo "Email recherché: $email\n";
echo "Utilisateur trouvé: " . ($user ? "OUI" : "NON") . "\n";

if ($user) {
    echo "Username: " . $user['username'] . "\n";
    echo "Email: " . $user['email'] . "\n";
    echo "Hash en base: " . substr($user['password'], 0, 30) . "...\n";
    
    $verify = password_verify($password, $user['password']);
    echo "Password vérifié: " . ($verify ? "✅ OUI" : "❌ NON") . "\n";
} else {
    echo "❌ Aucun utilisateur trouvé avec cet email\n";
}
