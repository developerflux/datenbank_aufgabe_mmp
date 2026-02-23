<?php
/**
 * Dashboard – persönlicher Speicherbereich des eingeloggten Users
 */

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/footer.php';

requireLogin();

$userId = currentUserId();
$user   = getUserById($userId);
$files  = getUserFiles($userId);

// Speichernutzung berechnen
$used     = (int)$user['storage_used'];
$limit    = (int)$user['storage_limit'];
$pct      = $limit > 0 ? min(100, round($used / $limit * 100)) : 0;
$barClass = $pct >= 90 ? 'danger' : ($pct >= 70 ? 'warning' : '');

renderHeader('Meine Dateien');
?>

<h1 class="page-heading">📁 Meine Dateien</h1>

<!-- Speicherübersicht -->
<div class="card">
    <h2>💾 Speichernutzung</h2>
    <div class="storage-bar-wrap">
        <div class="storage-bar">
            <div class="storage-bar-fill <?= $barClass ?>" style="width:<?= $pct ?>%"></div>
        </div>
        <p class="storage-info">
            <?= htmlspecialchars(formatBytes($used)) ?> von <?= htmlspecialchars(formatBytes($limit)) ?> genutzt
            (<?= $pct ?>%)
        </p>
    </div>
</div>

<!-- Upload-Formular -->
<div class="card">
    <h2>⬆️ Datei hochladen</h2>
    <?php if (isset($_GET['upload'])): ?>
        <?php if ($_GET['upload'] === 'ok'): ?>
            <div class="alert alert-success">Datei erfolgreich hochgeladen.</div>
        <?php else: ?>
            <div class="alert alert-error"><?= htmlspecialchars(urldecode($_GET['msg'] ?? 'Fehler beim Hochladen.')) ?></div>
        <?php endif; ?>
    <?php endif; ?>

    <form method="post" action="/upload.php" enctype="multipart/form-data">
        <div class="form-row">
            <div class="form-group">
                <label for="file">Datei auswählen</label>
                <input type="file" id="file" name="file" required>
            </div>
            <div>
                <button type="submit" class="btn btn-primary">Hochladen</button>
            </div>
        </div>
    </form>
</div>

<!-- Dateiliste -->
<div class="card">
    <h2>📄 Vorhandene Dateien</h2>
    <?php if (empty($files)): ?>
        <div class="empty-state">
            <div class="icon">📭</div>
            <p>Noch keine Dateien vorhanden. Lade deine erste Datei hoch!</p>
        </div>
    <?php else: ?>
        <table class="file-table">
            <thead>
                <tr>
                    <th>Dateiname</th>
                    <th>Größe</th>
                    <th>Hochgeladen am</th>
                    <th>Aktionen</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($files as $f): ?>
                <tr>
                    <td>📄 <?= htmlspecialchars($f['original_name']) ?></td>
                    <td><?= htmlspecialchars(formatBytes((int)$f['file_size'])) ?></td>
                    <td><?= htmlspecialchars(date('d.m.Y H:i', strtotime($f['uploaded_at']))) ?></td>
                    <td>
                        <a href="/download.php?id=<?= (int)$f['id'] ?>"
                           class="btn btn-primary btn-sm">⬇ Herunterladen</a>
                        <a href="/delete.php?id=<?= (int)$f['id'] ?>"
                           class="btn btn-danger btn-sm"
                           onclick="return confirm('Datei wirklich löschen?')">🗑 Löschen</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<!-- Freigegebener Ordner -->
<div class="card">
    <h2>🔗 Ordner freigeben</h2>
    <p style="margin-bottom:1rem;color:#6b7280;font-size:.9rem;">
        Gib einen Unterordner in deinem Bereich an, auf den andere Klassenmitglieder lesend zugreifen dürfen.
        Lasse das Feld leer, um keine Freigabe zu haben.
    </p>

    <?php if (isset($_GET['share'])): ?>
        <?php if ($_GET['share'] === 'ok'): ?>
            <div class="alert alert-success">Freigabe gespeichert.</div>
        <?php else: ?>
            <div class="alert alert-error"><?= htmlspecialchars(urldecode($_GET['msg'] ?? 'Fehler.')) ?></div>
        <?php endif; ?>
    <?php endif; ?>

    <form method="post" action="/set_shared.php">
        <div class="form-row">
            <div class="form-group">
                <label for="shared_folder">Freigegebener Pfad (relativ zu deinem Ordner)</label>
                <input type="text" id="shared_folder" name="shared_folder"
                       value="<?= htmlspecialchars($user['shared_folder'] ?? '') ?>"
                       placeholder="z.B. public oder leer lassen">
            </div>
            <div>
                <button type="submit" class="btn btn-primary">Speichern</button>
            </div>
        </div>
    </form>
</div>

<?php renderFooter(); ?>
