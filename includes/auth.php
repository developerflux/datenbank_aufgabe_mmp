<?php
/**
 * Authentifizierungs-Hilfsfunktionen
 */

require_once __DIR__ . '/../config/database.php';

define('UPLOAD_BASE_DIR', __DIR__ . '/../uploads');

/**
 * Startet die Session, falls noch nicht gestartet.
 */
function startSession(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

/**
 * Prüft ob der User eingeloggt ist; leitet sonst weiter.
 */
function requireLogin(): void {
    startSession();
    if (empty($_SESSION['user_id'])) {
        header('Location: /index.php');
        exit;
    }
}

/**
 * Gibt die ID des aktuell eingeloggten Users zurück oder null.
 */
function currentUserId(): ?int {
    startSession();
    return isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
}

/**
 * Gibt den Benutzernamen des aktuell eingeloggten Users zurück oder null.
 */
function currentUsername(): ?string {
    startSession();
    return $_SESSION['username'] ?? null;
}

/**
 * Lädt einen User anhand seiner ID.
 */
function getUserById(int $id): ?array {
    $stmt = getDB()->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    return $row ?: null;
}

/**
 * Lädt einen User anhand seines Usernamens.
 */
function getUserByUsername(string $username): ?array {
    $stmt = getDB()->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->execute([$username]);
    $row = $stmt->fetch();
    return $row ?: null;
}

/**
 * Prüft Passwort-Regeln.
 * Gibt ein Array mit Fehlermeldungen zurück (leer = gültig).
 */
function validatePassword(string $password): array {
    $errors = [];
    if (strlen($password) < 8) {
        $errors[] = 'Das Passwort muss mindestens 8 Zeichen lang sein.';
    }
    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = 'Das Passwort muss mindestens einen Großbuchstaben enthalten.';
    }
    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = 'Das Passwort muss mindestens einen Kleinbuchstaben enthalten.';
    }
    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = 'Das Passwort muss mindestens eine Zahl enthalten.';
    }
    if (!preg_match('/[^A-Za-z0-9]/', $password)) {
        $errors[] = 'Das Passwort muss mindestens ein Sonderzeichen enthalten.';
    }
    return $errors;
}

/**
 * Gibt den absoluten Upload-Pfad für einen User zurück und legt ihn an.
 * Wirft eine RuntimeException, wenn das Verzeichnis nicht erstellt werden kann.
 */
function getUserUploadDir(int $userId): string {
    $dir = UPLOAD_BASE_DIR . '/' . $userId;
    if (!is_dir($dir) && !mkdir($dir, 0750, true) && !is_dir($dir)) {
        throw new \RuntimeException("Upload-Verzeichnis konnte nicht angelegt werden: {$dir}");
    }
    return $dir;
}

/**
 * Gibt alle Dateien eines Users zurück.
 */
function getUserFiles(int $userId): array {
    $stmt = getDB()->prepare(
        'SELECT * FROM files WHERE user_id = ? ORDER BY uploaded_at DESC'
    );
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

/**
 * Gibt eine Datei anhand ihrer ID zurück und prüft optional den Eigentümer.
 */
function getFileById(int $fileId, ?int $ownerId = null): ?array {
    if ($ownerId !== null) {
        $stmt = getDB()->prepare('SELECT * FROM files WHERE id = ? AND user_id = ?');
        $stmt->execute([$fileId, $ownerId]);
    } else {
        $stmt = getDB()->prepare('SELECT * FROM files WHERE id = ?');
        $stmt->execute([$fileId]);
    }
    $row = $stmt->fetch();
    return $row ?: null;
}

/**
 * Formatiert eine Dateigröße als lesbare Zeichenkette.
 */
function formatBytes(int $bytes): string {
    if ($bytes >= 1073741824) {
        return round($bytes / 1073741824, 2) . ' GB';
    }
    if ($bytes >= 1048576) {
        return round($bytes / 1048576, 2) . ' MB';
    }
    if ($bytes >= 1024) {
        return round($bytes / 1024, 2) . ' KB';
    }
    return $bytes . ' B';
}

/**
 * Gibt alle User zurück, die einen freigegebenen Ordner haben.
 */
function getUsersWithSharedFolders(int $excludeUserId): array {
    $stmt = getDB()->prepare(
        'SELECT id, username, shared_folder FROM users
         WHERE shared_folder IS NOT NULL AND shared_folder != "" AND id != ?'
    );
    $stmt->execute([$excludeUserId]);
    return $stmt->fetchAll();
}

/**
 * Gibt den sicheren absoluten Pfad des freigegebenen Ordners zurück
 * oder null, wenn er ungültig / außerhalb von uploads ist.
 */
function resolveSharedPath(int $userId, string $relPath): ?string {
    $base   = realpath(UPLOAD_BASE_DIR . '/' . $userId);
    if ($base === false) {
        return null;
    }
    $target = realpath($base . '/' . ltrim($relPath, '/\\'));
    if ($target === false || strpos($target . '/', $base . '/') !== 0) {
        return null;
    }
    return $target;
}
