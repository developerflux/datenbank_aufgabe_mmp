<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MMP Lernplattform</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
    <header>
        <nav>
            <div class="container">
                <a href="index.php" class="logo">MMP Lernplattform</a>
                <ul>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="index.php?page=dashboard">Dashboard</a></li>
                        <li><a href="index.php?page=profile">Profil</a></li>
                        <li><a href="index.php?page=logout">Logout (<?php echo htmlspecialchars($_SESSION['username']); ?>)</a></li>
                    <?php else: ?>
                        <li><a href="index.php?page=login">Login</a></li>
                        <li><a href="index.php?page=register">Registrieren</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>
    </header>
    <main class="container">
