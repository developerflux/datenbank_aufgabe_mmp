<?php
/**
 * Gemeinsamer HTML-Kopf und Navigation
 *
 * @param string $pageTitle Titel der Seite
 */
function renderHeader(string $pageTitle = 'Klassen-Plattform'): void {
    $username = currentUsername();
    ?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> – Klassen-Plattform</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
<header class="site-header">
    <div class="container">
        <a href="/dashboard.php" class="logo">📚 Klassen-Plattform</a>
        <?php if ($username !== null): ?>
        <nav class="main-nav">
            <a href="/dashboard.php">Meine Dateien</a>
            <a href="/shared.php">Geteilte Dateien</a>
            <a href="/change_password.php">Passwort ändern</a>
            <a href="/logout.php" class="btn btn-outline">Abmelden</a>
        </nav>
        <span class="user-badge">👤 <?= htmlspecialchars($username) ?></span>
        <?php endif; ?>
    </div>
</header>
<main class="container">
    <?php
}
