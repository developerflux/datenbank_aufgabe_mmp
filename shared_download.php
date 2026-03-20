<?php
/**
 * Download-Handler für geteilte Dateien.
 * Prüft, dass die Datei im freigegebenen Ordner des Eigentümers liegt.
 */

require_once __DIR__ . '/includes/auth.php';

requireLogin();

$ownerId  = isset($_GET['owner']) ? (int)$_GET['owner']     : 0;
$fileName = isset($_GET['file'])  ? basename($_GET['file'])  : '';

if ($ownerId <= 0 || $fileName === '') {
    http_response_code(400);
    exit('Ungültige Anfrage.');
}

// Eigentümer-Datensatz laden
$owner = getUserById($ownerId);
if ($owner === null || empty($owner['shared_folder'])) {
    http_response_code(403);
    exit('Zugriff verweigert.');
}

// Freigegebenen Pfad auflösen
$resolvedDir = resolveSharedPath($ownerId, $owner['shared_folder']);
if ($resolvedDir === null || !is_dir($resolvedDir)) {
    http_response_code(404);
    exit('Freigegebener Ordner nicht gefunden.');
}

$filePath = $resolvedDir . '/' . $fileName;
// Sicherheitsprüfung: Datei muss im freigegebenen Verzeichnis liegen
$realFilePath = realpath($filePath);
if ($realFilePath === false
    || strpos($realFilePath . '/', $resolvedDir . '/') !== 0
    || !is_file($realFilePath)
) {
    http_response_code(403);
    exit('Zugriff verweigert.');
}

$finfo    = new finfo(FILEINFO_MIME_TYPE);
$mimeType = $finfo->file($realFilePath) ?: 'application/octet-stream';

header('Content-Type: ' . $mimeType);
$safeFilename = preg_replace('/[\x00-\x1f\x7f"\\\\]/', '_', $fileName);
header('Content-Disposition: attachment; filename="' . $safeFilename . '"; filename*=UTF-8\'\'' . rawurlencode($fileName));
header('Content-Length: ' . filesize($realFilePath));
header('Cache-Control: no-store');
readfile($realFilePath);
exit;
