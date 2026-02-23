<?php
/**
 * Registrierungsseite
 */

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/footer.php';

startSession();

if (currentUserId() !== null) {
    header('Location: /dashboard.php');
    exit;
}

$errors  = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username  = trim($_POST['username'] ?? '');
    $password  = $_POST['password']  ?? '';
    $password2 = $_POST['password2'] ?? '';

    // Benutzername validieren
    if ($username === '') {
        $errors[] = 'Benutzername darf nicht leer sein.';
    } elseif (strlen($username) < 3 || strlen($username) > 50) {
        $errors[] = 'Benutzername muss zwischen 3 und 50 Zeichen lang sein.';
    } elseif (!preg_match('/^[A-Za-z0-9_.\-]+$/', $username)) {
        $errors[] = 'Benutzername darf nur Buchstaben, Zahlen, Unterstriche, Punkte und Bindestriche enthalten.';
    } elseif (getUserByUsername($username) !== null) {
        $errors[] = 'Dieser Benutzername ist bereits vergeben.';
    }

    // Passwort validieren
    $pwErrors = validatePassword($password);
    $errors   = array_merge($errors, $pwErrors);

    if ($password !== $password2) {
        $errors[] = 'Die Passwörter stimmen nicht überein.';
    }

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = getDB()->prepare(
            'INSERT INTO users (username, password_hash) VALUES (?, ?)'
        );
        $stmt->execute([$username, $hash]);

        // Verzeichnis anlegen
        getUserUploadDir((int)getDB()->lastInsertId());

        $success = true;
    }
}

renderHeader('Registrieren');
?>

<div class="auth-wrap">
    <div class="auth-card">
        <h1>📝 Registrieren</h1>
        <p class="auth-sub">Neues Konto erstellen</p>

        <?php if ($success): ?>
            <div class="alert alert-success">
                Konto erfolgreich erstellt! <a href="/index.php">Jetzt anmelden</a>
            </div>
        <?php else: ?>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <ul>
                        <?php foreach ($errors as $e): ?>
                            <li><?= htmlspecialchars($e) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="post" action="/register.php" novalidate>
                <div class="form-group">
                    <label for="username">Benutzername</label>
                    <input type="text" id="username" name="username"
                           value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                           autocomplete="username" required autofocus>
                </div>
                <div class="form-group">
                    <label for="password">Passwort</label>
                    <input type="password" id="password" name="password"
                           autocomplete="new-password" required>
                    <ul class="password-hint">
                        <li>Mindestens 8 Zeichen</li>
                        <li>Groß- und Kleinbuchstaben</li>
                        <li>Mindestens eine Zahl</li>
                        <li>Mindestens ein Sonderzeichen</li>
                    </ul>
                </div>
                <div class="form-group">
                    <label for="password2">Passwort bestätigen</label>
                    <input type="password" id="password2" name="password2"
                           autocomplete="new-password" required>
                </div>
                <button type="submit" class="btn btn-primary">Konto erstellen</button>
            </form>

        <?php endif; ?>

        <a class="auth-link" href="/index.php">Bereits registriert? Jetzt anmelden</a>
    </div>
</div>

<?php renderFooter(); ?>
