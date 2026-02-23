<?php
/**
 * Abmelden und Session zerstören
 */

require_once __DIR__ . '/includes/auth.php';

startSession();
$_SESSION = [];
session_destroy();
header('Location: /index.php');
exit;
