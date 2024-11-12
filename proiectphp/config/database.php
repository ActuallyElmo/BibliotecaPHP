<?php
$host = 'mysql-bibliotecasa.alwaysdata.net';  // e.g., 'localhost' or 'mysql.hostingprovider.com'
$dbname = 'bibliotecasa_database';
$user = '384904';
$password = 'Parola123!';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>