<?php
$conn_string = getenv('DATABASE_URL');
if (!$conn_string) {
    die("DATABASE_URL not set");
}

$db = pg_connect($conn_string);
if (!$db) {
    die("Connection failed: " . pg_last_error());
}
?>
