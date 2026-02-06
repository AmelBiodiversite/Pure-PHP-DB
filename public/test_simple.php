<?php
// test_simple.php - TEST BASIQUE POUR VOIR SI PHP AFFICHE QUELQUE CHOSE

// On force le type de contenu HTML
header('Content-Type: text/html; charset=utf-8');

// On commence directement le HTML
echo '<!DOCTYPE html>';
echo '<html lang="fr">';
echo '<head>';
echo '  <meta charset="utf-8">';
echo '  <title>Test Simple PHP - MarketFlow</title>';
echo '  <style>body { font-family: sans-serif; padding: 60px; background: #f0f8ff; text-align: center; }</style>';
echo '</head>';
echo '<body>';

echo '<h1 style="color: #006400; font-size: 3em;">TEST RÉUSSI !</h1>';
echo '<p style="font-size: 1.5em; color: #333;">Si tu vois ce titre vert et ce texte → PHP fonctionne parfaitement sur Railway.</p>';
echo '<p style="font-size: 1.3em; color: #555; margin-top: 40px;">';
echo 'Cette page n\'utilise ni require, ni variables, ni PHPMailer.<br>';
echo 'Elle prouve que le serveur renvoie du contenu HTML.';
echo '</p>';

echo '<hr style="border: 2px solid #006400; margin: 50px auto; width: 60%;">';
echo '<small style="color: #777;">Date serveur : ' . date('d/m/Y H:i:s') . ' (vérifie que c\'est à jour)</small>';

echo '</body>';
echo '</html>';
