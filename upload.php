<?php
/**
 * Datei-Upload-Handler
 * Speichert die Datei im persönlichen Verzeichnis des Users.
 */

require_once __DIR__ . '/includes/auth.php';

requireLogin();

$userId = currentUserId();
$user   = getUserById($userId);

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['file'])) {
    header('Location: /dashboard.php');
    exit;
}

$file = $_FILES['file'];

// Upload-Fehler des PHP-Upload-Mechanismus prüfen
if ($file['error'] !== UPLOAD_ERR_OK) {
    $msg = urlencode('Upload-Fehler (Code ' . $file['error'] . ').');
    header("Location: /dashboard.php?upload=fail&msg={$msg}");
    exit;
}

$fileSize = (int)$file['size'];
if ($fileSize === 0) {
    $msg = urlencode('Die Datei ist leer.');
    header("Location: /dashboard.php?upload=fail&msg={$msg}");
    exit;
}

// Speicherlimit prüfen
$used  = (int)$user['storage_used'];
$limit = (int)$user['storage_limit'];
if ($used + $fileSize > $limit) {
    $remaining = formatBytes($limit - $used);
    $msg = urlencode("Speicherlimit überschritten. Noch verfügbar: {$remaining}.");
    header("Location: /dashboard.php?upload=fail&msg={$msg}");
    exit;
}

// Dateinamen sichern: nur den Basisnamen verwenden, eindeutigen internen Namen vergeben
$originalName = basename($file['name']);
$extension    = pathinfo($originalName, PATHINFO_EXTENSION);
$storedName   = bin2hex(random_bytes(16)) . ($extension !== '' ? '.' . $extension : '');

$uploadDir  = getUserUploadDir($userId);
$targetPath = $uploadDir . '/' . $storedName;

if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
    $msg = urlencode('Die Datei konnte nicht gespeichert werden.');
    header("Location: /dashboard.php?upload=fail&msg={$msg}");
    exit;
}

// MIME-Typ vor dem Speichern der DB-Einträge ermitteln
$mimeType = mime_content_type($targetPath) ?: 'application/octet-stream';

// Datenbank aktualisieren
$db = getDB();
$db->beginTransaction();
try {
    $stmt = $db->prepare(
        'INSERT INTO files (user_id, filename, original_name, file_size, mime_type) VALUES (?, ?, ?, ?, ?)'
    );
    $stmt->execute([$userId, $storedName, $originalName, $fileSize, $mimeType]);

    $db->prepare('UPDATE users SET storage_used = storage_used + ? WHERE id = ?')
       ->execute([$fileSize, $userId]);

    $db->commit();
} catch (Exception $e) {
    $db->rollBack();
    @unlink($targetPath);
    $msg = urlencode('Datenbankfehler beim Speichern der Datei.');
    header("Location: /dashboard.php?upload=fail&msg={$msg}");
    exit;
}

header('Location: /dashboard.php?upload=ok');
exit;
