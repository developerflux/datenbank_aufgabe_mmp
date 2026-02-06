<?php
$error = '';
$login_username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $login_username = $username;
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = 'Bitte alle Felder ausfüllen.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute(array($username));
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header('Location: index.php?page=dashboard');
            exit;
        } else {
            $error = 'Ungültiger Benutzername oder Passwort.';
        }
    }
}
?>

<div class="form-container">
    <h2>Login</h2>
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    <form action="index.php?page=login" method="POST">
        <div class="form-group">
            <label for="username">Benutzername</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($login_username); ?>" required>
        </div>
        <div class="form-group">
            <label for="password">Passwort</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit">Einloggen</button>
    </form>
    <p style="margin-top: 1rem; text-align: center;">
        Noch kein Konto? <a href="index.php?page=register">Hier registrieren</a>
    </p>
</div>
