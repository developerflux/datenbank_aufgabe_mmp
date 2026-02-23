<?php
/**
 * Datei-Download-Handler
 * Prüft Eigentümerschaft und liefert die Datei aus.
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
if (!is_file($filePath)) {
    http_response_code(404);
    exit('Datei nicht gefunden.');
}

$mimeType = $file['mime_type'] ?: 'application/octet-stream';

header('Content-Type: ' . $mimeType);
header('Content-Disposition: attachment; filename="' . str_replace('"', '\\"', $file['original_name']) . '"');
header('Content-Length: ' . filesize($filePath));
header('Cache-Control: no-store');
readfile($filePath);
exit;
