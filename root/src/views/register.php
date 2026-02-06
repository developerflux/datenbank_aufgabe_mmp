<?php
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    if (empty($username) || empty($password)) {
        $error = 'Bitte alle Felder ausfüllen.';
    } elseif (!preg_match('/^[a-zA-Z0-9_]{3,50}$/', $username)) {
        $error = 'Benutzername darf nur Buchstaben, Zahlen und Unterstriche enthalten (3-50 Zeichen).';
    } elseif ($password !== $password_confirm) {
        $error = 'Passwörter stimmen nicht überein.';
    } elseif (strlen($password) < 8) {
        $error = 'Das Passwort muss mindestens 8 Zeichen lang sein.';
    } else {
        // Prüfen, ob User bereits existiert
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute(array($username));
        if ($stmt->fetch()) {
            $error = 'Benutzername bereits vergeben.';
        } else {
            // User anlegen
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            if ($stmt->execute(array($username, $hashed_password))) {
                // Ordner für User erstellen
                $user_dir = 'user_data/' . $username;
                if (!is_dir($user_dir)) {
                    mkdir($user_dir, 0777, true);
                }
                $success = 'Registrierung erfolgreich! Sie können sich nun einloggen.';
            } else {
                $error = 'Fehler bei der Registrierung.';
            }
        }
    }
}
?>

<div class="form-container">
    <h2>Registrieren</h2>
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    <form action="index.php?page=register" method="POST">
        <div class="form-group">
            <label for="username">Benutzername</label>
            <input type="text" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="password">Passwort</label>
            <input type="password" id="password" name="password" required>
        </div>
        <div class="form-group">
            <label for="password_confirm">Passwort bestätigen</label>
            <input type="password" id="password_confirm" name="password_confirm" required>
        </div>
        <button type="submit">Registrieren</button>
    </form>
    <p style="margin-top: 1rem; text-align: center;">
        Bereits ein Konto? <a href="index.php?page=login">Hier einloggen</a>
    </p>
</div>
