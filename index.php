<?php
require 'db.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Pur</title>
    <style>
        body { font-family: sans-serif; padding: 2rem; line-height: 1.5; }
        .success { color: green; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Environnement PHP Pur</h1>
    
    <?php
    if ($db) {
        echo "<p class='success'>Connexion à la base de données établie avec succès.</p>";
        
        $result = pg_query($db, "SELECT NOW() as time, version()");
        if ($result) {
            $row = pg_fetch_assoc($result);
            echo "<p><strong>Heure serveur :</strong> " . $row['time'] . "</p>";
            echo "<p><strong>Version DB :</strong> " . $row['version'] . "</p>";
        }
    }
    ?>

    <p>Aucun framework, aucun Node.js, juste du PHP.</p>
</body>
</html>
