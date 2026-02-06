<?php
$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Aktuelle User-Daten laden
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute(array($user_id));
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['change_password'])) {
        $old_password = $_POST['old_password'];
        $new_password = $_POST['new_password'];
        $new_password_confirm = $_POST['new_password_confirm'];

        if (!password_verify($old_password, $user['password'])) {
            $error = 'Das alte Passwort ist nicht korrekt.';
        } elseif ($new_password !== $new_password_confirm) {
            $error = 'Die neuen Passwörter stimmen nicht überein.';
        } elseif (strlen($new_password) < 8) {
            $error = 'Das neue Passwort muss mindestens 8 Zeichen lang sein.';
        } else {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute(array($hashed_password, $user_id));
            $success = 'Passwort erfolgreich geändert.';
        }
    } elseif (isset($_POST['update_shared_path'])) {
        $shared_path = trim($_POST['shared_path']);
        // Einfache Validierung: Pfad muss im User-Ordner liegen
        $user_dir = 'user_data/' . $user['username'];
        
        if (!empty($shared_path) && strpos($shared_path, '..') !== false) {
            $error = 'Ungültiger Pfad.';
        } else {
            $stmt = $pdo->prepare("UPDATE users SET shared_path = ? WHERE id = ?");
            $stmt->execute(array($shared_path, $user_id));
            $success = 'Freigabepfad aktualisiert.';
            $user['shared_path'] = $shared_path;
        }
    }
}

// Alle freigegebenen Ordner anderer User abrufen
$stmt = $pdo->prepare("SELECT username, shared_path FROM users WHERE shared_path IS NOT NULL AND shared_path != '' AND id != ?");
$stmt->execute(array($user_id));
$shared_folders = $stmt->fetchAll();
?>

<h2>Mein Profil</h2>

<?php if ($error): ?>
    <div class="alert alert-error"><?php echo $error; ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
    <section>
        <div class="form-container" style="margin: 0; max-width: none;">
            <h3>Passwort ändern</h3>
            <form action="index.php?page=profile" method="POST">
                <input type="hidden" name="change_password" value="1">
                <div class="form-group">
                    <label for="old_password">Altes Passwort</label>
                    <input type="password" id="old_password" name="old_password" required>
                </div>
                <div class="form-group">
                    <label for="new_password">Neues Passwort</label>
                    <input type="password" id="new_password" name="new_password" required>
                </div>
                <div class="form-group">
                    <label for="new_password_confirm">Neues Passwort bestätigen</label>
                    <input type="password" id="new_password_confirm" name="new_password_confirm" required>
                </div>
                <button type="submit">Passwort aktualisieren</button>
            </form>
        </div>
    </section>

    <section>
        <div class="form-container" style="margin: 0; max-width: none;">
            <h3>Ordnerfreigabe</h3>
            <p>Definiere einen Pfad (relativ zu deinem Speicherbereich), den andere lesen dürfen.</p>
            <form action="index.php?page=profile" method="POST">
                <input type="hidden" name="update_shared_path" value="1">
                <div class="form-group">
                    <label for="shared_path">Freigabepfad (z.B. 'public_docs')</label>
                    <input type="text" id="shared_path" name="shared_path" value="<?php echo htmlspecialchars(isset($user['shared_path']) ? $user['shared_path'] : ''); ?>">
                </div>
                <button type="submit">Freigabe speichern</button>
            </form>
        </div>

        <div class="file-list" style="margin-top: 1rem;">
            <h3>Freigaben von anderen</h3>
            <?php if (empty($shared_folders)): ?>
                <p>Momentan keine Freigaben vorhanden.</p>
            <?php else: ?>
                <ul>
                    <?php foreach ($shared_folders as $folder): ?>
                        <li>
                            <strong><?php echo htmlspecialchars($folder['username']); ?>:</strong> 
                            <?php echo htmlspecialchars($folder['shared_path']); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </section>
</div>
