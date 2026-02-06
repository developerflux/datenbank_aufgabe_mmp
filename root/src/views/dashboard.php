<?php
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$user_dir = 'user_data/' . $username . '/';
$error = '';
$success = '';

// Benutzerverzeichnis sicherstellen
if (!is_dir($user_dir)) {
    mkdir($user_dir, 0750, true);
}

// Speicherlimit abrufen
$stmt = $pdo->prepare("SELECT storage_limit FROM users WHERE id = ?");
$stmt->execute(array($user_id));
$user_info = $stmt->fetch();
$storage_limit_mb = $user_info['storage_limit'];

// Aktuellen Speicherverbrauch berechnen
$stmt = $pdo->prepare("SELECT SUM(filesize) as total_size FROM files WHERE user_id = ?");
$stmt->execute(array($user_id));
$storage_usage = $stmt->fetch();
$current_usage_bytes = isset($storage_usage['total_size']) ? $storage_usage['total_size'] : 0;
$current_usage_mb = round($current_usage_bytes / (1024 * 1024), 2);

// Datei löschen
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_file'])) {
    $file_id = intval($_POST['delete_file']);
    $stmt = $pdo->prepare("SELECT * FROM files WHERE id = ? AND user_id = ?");
    $stmt->execute(array($file_id, $user_id));
    $file_to_delete = $stmt->fetch();

    if ($file_to_delete) {
        if (file_exists($file_to_delete['filepath'])) {
            unlink($file_to_delete['filepath']);
        }
        $stmt = $pdo->prepare("DELETE FROM files WHERE id = ? AND user_id = ?");
        $stmt->execute(array($file_id, $user_id));
        $current_usage_bytes -= $file_to_delete['filesize'];
        $current_usage_mb = round($current_usage_bytes / (1024 * 1024), 2);
        $success = 'Datei erfolgreich gelöscht.';
    } else {
        $error = 'Datei nicht gefunden oder keine Berechtigung.';
    }
}

// Datei-Upload verarbeiten (mehrere Dateien möglich)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file_upload'])) {
    $uploaded_count = 0;
    $errors = array();
    $file_count = count($_FILES['file_upload']['name']);
    $batch_filenames = array();

    for ($i = 0; $i < $file_count; $i++) {
        $filename = basename($_FILES['file_upload']['name'][$i]);
        $filesize = $_FILES['file_upload']['size'][$i];
        $tmp_name = $_FILES['file_upload']['tmp_name'][$i];
        $file_error = $_FILES['file_upload']['error'][$i];
        $target_file = $user_dir . $filename;

        // Validierung
        if ($file_error !== UPLOAD_ERR_OK) {
            $errors[] = htmlspecialchars($filename) . ': Fehler beim Upload.';
        } elseif (($current_usage_bytes + $filesize) > ($storage_limit_mb * 1024 * 1024)) {
            $errors[] = htmlspecialchars($filename) . ': Speicherlimit überschritten! Limit: ' . $storage_limit_mb . ' MB.';
        } elseif (file_exists($target_file) || in_array($filename, $batch_filenames)) {
            $errors[] = htmlspecialchars($filename) . ': Eine Datei mit diesem Namen existiert bereits.';
        } else {
            if (move_uploaded_file($tmp_name, $target_file)) {
                $stmt = $pdo->prepare("INSERT INTO files (user_id, filename, filepath, filesize) VALUES (?, ?, ?, ?)");
                $stmt->execute(array($user_id, $filename, $target_file, $filesize));
                $uploaded_count++;
                $batch_filenames[] = $filename;
                $current_usage_bytes += $filesize;
                $current_usage_mb = round($current_usage_bytes / (1024 * 1024), 2);
            } else {
                $errors[] = htmlspecialchars($filename) . ': Fehler beim Speichern der Datei.';
            }
        }
    }

    if ($uploaded_count > 0) {
        $success = $uploaded_count . ' Datei(en) erfolgreich hochgeladen.';
    }
    if (!empty($errors)) {
        $error = implode('<br>', $errors);
    }
}

// Dateien des Users abrufen
$stmt = $pdo->prepare("SELECT * FROM files WHERE user_id = ? ORDER BY upload_date DESC");
$stmt->execute(array($user_id));
$files = $stmt->fetchAll();
?>

<h2>Mein Dashboard</h2>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 2rem;">
    <section>
        <div class="form-container" style="margin: 0; max-width: none;">
            <h3>Datei hochladen</h3>
            <p>Speicherverbrauch: <?php echo $current_usage_mb; ?> / <?php echo $storage_limit_mb; ?> MB</p>
            <div style="background: #eee; height: 10px; border-radius: 5px; margin-bottom: 1rem;">
                <div style="background: var(--primary-color); height: 100%; width: <?php echo min(100, ($storage_limit_mb > 0 ? ($current_usage_mb / $storage_limit_mb) * 100 : 0)); ?>%; border-radius: 5px;"></div>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <form action="index.php?page=dashboard" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <input type="file" name="file_upload[]" multiple required>
                </div>
                <button type="submit">Hochladen</button>
            </form>
        </div>
    </section>

    <section>
        <div class="file-list">
            <h3>Meine Dateien</h3>
            <?php if (empty($files)): ?>
                <p>Noch keine Dateien hochgeladen.</p>
            <?php else: ?>
                <?php foreach ($files as $file): ?>
                    <div class="file-item">
                        <div>
                            <strong><?php echo htmlspecialchars($file['filename']); ?></strong><br>
                            <small><?php echo round($file['filesize'] / 1024, 2); ?> KB | <?php echo $file['upload_date']; ?></small>
                        </div>
                        <div>
                            <a href="<?php echo htmlspecialchars($file['filepath']); ?>" download style="color: var(--primary-color); text-decoration: none;">Download</a>
                            <form action="index.php?page=dashboard" method="POST" style="display: inline; margin-left: 0.5rem;">
                                <input type="hidden" name="delete_file" value="<?php echo intval($file['id']); ?>">
                                <button type="submit" onclick="return confirm('Datei wirklich löschen?');" style="background: var(--error-color); padding: 0.3rem 0.7rem; width: auto; font-size: 0.85rem;">Löschen</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>
</div>
