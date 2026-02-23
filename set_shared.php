<?php
/**
 * Freigegebenen Ordner setzen
 */

require_once __DIR__ . '/includes/auth.php';

requireLogin();

$userId = currentUserId();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /dashboard.php');
    exit;
}

$sharedFolder = trim($_POST['shared_folder'] ?? '');

if ($sharedFolder === '') {
    // Freigabe entfernen
    getDB()->prepare('UPDATE users SET shared_folder = NULL WHERE id = ?')
           ->execute([$userId]);
    header('Location: /dashboard.php?share=ok');
    exit;
}

// Pfad validieren: Darf nicht auf übergeordnete Verzeichnisse zugreifen
if (strpos($sharedFolder, '..') !== false || strpos($sharedFolder, '/') === 0) {
    $msg = urlencode('Ungültiger Pfad. Keine ".." oder absolute Pfade erlaubt.');
    header("Location: /dashboard.php?share=fail&msg={$msg}");
    exit;
}

// Prüfen ob der Pfad im Upload-Verzeichnis des Users existiert (oder anlegen)
$uploadDir  = getUserUploadDir($userId);
$targetPath = $uploadDir . '/' . $sharedFolder;

// Verzeichnis anlegen falls nicht vorhanden
if (!is_dir($targetPath)) {
    if (!mkdir($targetPath, 0750, true)) {
        $msg = urlencode('Der Ordner konnte nicht angelegt werden.');
        header("Location: /dashboard.php?share=fail&msg={$msg}");
        exit;
    }
}

// Nochmals auflösen und sicherstellen, dass er innerhalb von uploads liegt
$resolved = realpath($targetPath);
$base     = realpath($uploadDir);
if ($resolved === false || $base === false || strpos($resolved . '/', $base . '/') !== 0) {
    $msg = urlencode('Ungültiger Pfad.');
    header("Location: /dashboard.php?share=fail&msg={$msg}");
    exit;
}

getDB()->prepare('UPDATE users SET shared_folder = ? WHERE id = ?')
       ->execute([$sharedFolder, $userId]);

header('Location: /dashboard.php?share=ok');
exit;
