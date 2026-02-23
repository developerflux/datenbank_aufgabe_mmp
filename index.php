<?php
/**
 * Startseite / Login-Formular
 */

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/footer.php';

startSession();

// Bereits eingeloggte User direkt zum Dashboard
if (currentUserId() !== null) {
    header('Location: /dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Bitte Benutzername und Passwort eingeben.';
    } else {
        $user = getUserByUsername($username);
        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];
            header('Location: /dashboard.php');
            exit;
        } else {
            $error = 'Ungültiger Benutzername oder Passwort.';
        }
    }
}

renderHeader('Anmelden');
?>

<div class="auth-wrap">
    <div class="auth-card">
        <h1>🔐 Anmelden</h1>
        <p class="auth-sub">Klassen-Plattform – privater Bereich</p>

        <?php if ($error !== ''): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post" action="/index.php" novalidate>
            <div class="form-group">
                <label for="username">Benutzername</label>
                <input type="text" id="username" name="username"
                       value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                       autocomplete="username" required autofocus>
            </div>
            <div class="form-group">
                <label for="password">Passwort</label>
                <input type="password" id="password" name="password"
                       autocomplete="current-password" required>
            </div>
            <button type="submit" class="btn btn-primary">Anmelden</button>
        </form>

        <a class="auth-link" href="/register.php">Noch kein Konto? Jetzt registrieren</a>
    </div>
</div>

<?php renderFooter(); ?>
