<?php
/**
 * Passwort ändern
 */

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/footer.php';

requireLogin();

$userId  = currentUserId();
$errors  = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current   = $_POST['current_password']  ?? '';
    $newPw     = $_POST['new_password']       ?? '';
    $newPw2    = $_POST['new_password2']      ?? '';

    $user = getUserById($userId);

    // Aktuelles Passwort prüfen
    if ($user === null || !password_verify($current, $user['password_hash'])) {
        $errors[] = 'Das aktuelle Passwort ist falsch.';
    }

    // Neues Passwort validieren
    $pwErrors = validatePassword($newPw);
    $errors   = array_merge($errors, $pwErrors);

    if ($newPw !== $newPw2) {
        $errors[] = 'Die neuen Passwörter stimmen nicht überein.';
    }

    if (empty($errors)) {
        $hash = password_hash($newPw, PASSWORD_BCRYPT);
        getDB()->prepare('UPDATE users SET password_hash = ? WHERE id = ?')
               ->execute([$hash, $userId]);
        $success = true;
    }
}

renderHeader('Passwort ändern');
?>

<div class="auth-wrap">
    <div class="auth-card">
        <h1>🔑 Passwort ändern</h1>
        <p class="auth-sub">Eingeloggt als <?= htmlspecialchars(currentUsername()) ?></p>

        <?php if ($success): ?>
            <div class="alert alert-success">Passwort erfolgreich geändert.</div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <ul>
                    <?php foreach ($errors as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" action="/change_password.php" novalidate>
            <div class="form-group">
                <label for="current_password">Aktuelles Passwort</label>
                <input type="password" id="current_password" name="current_password"
                       autocomplete="current-password" required autofocus>
            </div>
            <div class="form-group">
                <label for="new_password">Neues Passwort</label>
                <input type="password" id="new_password" name="new_password"
                       autocomplete="new-password" required>
                <ul class="password-hint">
                    <li>Mindestens 8 Zeichen</li>
                    <li>Groß- und Kleinbuchstaben</li>
                    <li>Mindestens eine Zahl</li>
                    <li>Mindestens ein Sonderzeichen</li>
                </ul>
            </div>
            <div class="form-group">
                <label for="new_password2">Neues Passwort bestätigen</label>
                <input type="password" id="new_password2" name="new_password2"
                       autocomplete="new-password" required>
            </div>
            <button type="submit" class="btn btn-primary">Passwort ändern</button>
        </form>

        <a class="auth-link" href="/dashboard.php">← Zurück zum Dashboard</a>
    </div>
</div>

<?php renderFooter(); ?>
