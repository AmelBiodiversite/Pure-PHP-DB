<?php
header('Content-Type: text/plain');

echo "=== TEST PDO POSTGRESQL ===\n\n";

echo "1. Extensions PDO disponibles :\n";
print_r(PDO::getAvailableDrivers());

echo "\n\n2. Variable DATABASE_URL :\n";
$dbUrl = getenv('DATABASE_URL');
echo $dbUrl ? "Définie : " . substr($dbUrl, 0, 30) . "..." : "NON DÉFINIE";

echo "\n\n3. Test connexion PostgreSQL :\n";
try {
    $parts = parse_url($dbUrl);
    $dsn = sprintf(
        "pgsql:host=%s;port=%d;dbname=%s",
        $parts['host'],
        $parts['port'] ?? 5432,
        ltrim($parts['path'], '/')
    );
    echo "DSN construit : " . $dsn . "\n";
    
    $pdo = new PDO($dsn, $parts['user'], $parts['pass']);
    echo "✅ CONNEXION RÉUSSIE !\n";
} catch (Exception $e) {
    echo "❌ ERREUR : " . $e->getMessage() . "\n";
}
?>
