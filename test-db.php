<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>🔍 Diagnostic Connexion PostgreSQL</h1>";

// Test 1: Extensions
echo "<h2>✅ Extensions disponibles</h2>";
echo "<p>PDO Drivers : " . implode(', ', PDO::getAvailableDrivers()) . "</p>";

// Test 2: DATABASE_URL
$url = getenv('DATABASE_URL');
echo "<h2>✅ DATABASE_URL</h2>";
echo "<p>URL brute : <code>" . htmlspecialchars($url) . "</code></p>";

// Test 3: Parser l'URL
if ($url) {
    $parts = parse_url($url);
    echo "<h2>✅ URL parsée</h2>";
    echo "<pre>";
    print_r($parts);
    echo "</pre>";

    // Extraire les infos
    $scheme = $parts['scheme'] ?? '';
    $host = $parts['host'] ?? 'localhost';
    $port = $parts['port'] ?? 5432;
    $dbname = ltrim($parts['path'] ?? '', '/');
    $user = $parts['user'] ?? 'postgres';
    $pass = $parts['pass'] ?? '';

    echo "<h2>🔧 Tentative de connexion</h2>";
    echo "<p><strong>Scheme:</strong> $scheme</p>";
    echo "<p><strong>Host:</strong> $host</p>";
    echo "<p><strong>Port:</strong> $port</p>";
    echo "<p><strong>Database:</strong> $dbname</p>";
    echo "<p><strong>User:</strong> $user</p>";

    // Test connexion 1: Avec postgresql:// dans l'URL
    echo "<h3>Test 1: Connexion directe avec URL</h3>";
    try {
        $pdo1 = new PDO($url);
        echo "<p>✅ <strong>SUCCÈS avec URL directe !</strong></p>";
    } catch (PDOException $e) {
        echo "<p>❌ Échec : " . htmlspecialchars($e->getMessage()) . "</p>";
    }

    // Test connexion 2: Avec DSN construit
    echo "<h3>Test 2: Connexion avec DSN pgsql://</h3>";
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
    echo "<p>DSN : <code>$dsn</code></p>";

    try {
        $pdo2 = new PDO($dsn, $user, $pass);
        echo "<p>✅ <strong>SUCCÈS avec DSN construit !</strong></p>";

        // Test requête
        $result = $pdo2->query("SELECT version()");
        $version = $result->fetch();
        echo "<p><strong>Version PostgreSQL :</strong> " . htmlspecialchars($version['version']) . "</p>";

    } catch (PDOException $e) {
        echo "<p>❌ Échec : " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}

    echo "<h2>👥 Utilisateurs dans la base</h2>";
    try {
        $pdo2 = new PDO("pgsql:host=helium;port=5432;dbname=heliumdb", "postgres", "password");
        $result = $pdo2->query("SELECT id, email, username, role, created_at FROM users ORDER BY id");
        $users = $result->fetchAll();

        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Email</th><th>Username</th><th>Role</th><th>Créé le</th></tr>";
        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>{$user['id']}</td>";
            echo "<td>{$user['email']}</td>";
            echo "<td>{$user['username']}</td>";
            echo "<td>{$user['role']}</td>";
            echo "<td>{$user['created_at']}</td>";
            echo "</tr>";
        }
        echo "</table>";

    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
    
?>