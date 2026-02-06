<?php
// Datenbank-Konfiguration für USBWebserver
define('DB_HOST', 'localhost:3307');
define('DB_USER', 'root');
define('DB_PASS', 'usbw');
define('DB_NAME', 'mmp_learning_platform');

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Datenbankverbindung fehlgeschlagen: " . $e->getMessage());
}
?>
