<?php
echo "<h1>Extensions PHP Web</h1>";
echo "<pre>";
print_r(get_loaded_extensions());
echo "</pre>";

echo "<h1>PDO Drivers disponibles</h1>";
echo "<pre>";
print_r(PDO::getAvailableDrivers());
echo "</pre>";

echo "<hr>";
phpinfo();
?>