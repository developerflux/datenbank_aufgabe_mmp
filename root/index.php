<?php
ob_start();
session_start();
require_once 'config/db.php';
require_once 'src/utils/password_compat.php';

// Einfaches Routing
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// Redirects vor jeglicher HTML-Ausgabe verarbeiten
switch ($page) {
    case 'dashboard':
    case 'profile':
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }
        break;
    case 'logout':
        session_destroy();
        header('Location: index.php?page=login');
        exit;
}

// Header einbinden
include 'src/includes/header.php';

// Content basierend auf der Seite laden
switch ($page) {
    case 'login':
        include 'src/views/login.php';
        break;
    case 'register':
        include 'src/views/register.php';
        break;
    case 'dashboard':
        include 'src/views/dashboard.php';
        break;
    case 'profile':
        include 'src/views/profile.php';
        break;
    default:
        include 'src/views/home.php';
        break;
}

// Footer einbinden
include 'src/includes/footer.php';
ob_end_flush();
?>
