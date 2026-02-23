<?php
/**
 * Datei-Lösch-Handler
 * Prüft Eigentümerschaft und löscht Datei + Datenbankeintrag.
 */

require_once __DIR__ . '/includes/auth.php';

requireLogin();

$userId = currentUserId();
$fileId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$file = getFileById($fileId, $userId);
if ($file === null) {
    http_response_code(403);
    exit('Zugriff verweigert.');
}

$filePath = getUserUploadDir($userId) . '/' . $file['filename'];

$db = getDB();
$db->beginTransaction();
try {
    $db->prepare('DELETE FROM files WHERE id = ? AND user_id = ?')
       ->execute([$fileId, $userId]);

    $db->prepare('UPDATE users SET storage_used = GREATEST(0, storage_used - ?) WHERE id = ?')
       ->execute([(int)$file['file_size'], $userId]);

    $db->commit();
} catch (Exception $e) {
    $db->rollBack();
    header('Location: /dashboard.php?upload=fail&msg=' . urlencode('Fehler beim Löschen.'));
    exit;
}

if (is_file($filePath)) {
    @unlink($filePath);
}

header('Location: /dashboard.php');
exit;
