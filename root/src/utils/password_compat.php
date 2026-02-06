<?php
/**
 * Hilfsfunktionen für Passwort-Hashing auf alten PHP-Versionen
 */

if (!function_exists('password_hash')) {
    function password_hash($password, $algo) {
        // Fallback auf SHA256 für sehr alte PHP-Versionen
        return hash('sha256', $password);
    }
}

if (!function_exists('password_verify')) {
    function password_verify($password, $hash) {
        // Fallback-Vergleich
        return hash('sha256', $password) === $hash;
    }
}

if (!defined('PASSWORD_DEFAULT')) {
    define('PASSWORD_DEFAULT', 1);
}
?>
