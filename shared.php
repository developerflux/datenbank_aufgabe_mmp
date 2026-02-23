<?php
/**
 * Geteilte Dateien anderer User anzeigen und herunterladen
 */

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/footer.php';

requireLogin();

$userId = currentUserId();

// Alle User mit freigegebenen Ordnern laden
$sharedUsers = getUsersWithSharedFolders($userId);

renderHeader('Geteilte Dateien');
?>

<h1 class="page-heading">🔗 Geteilte Dateien</h1>

<?php if (empty($sharedUsers)): ?>
    <div class="card">
        <div class="empty-state">
            <div class="icon">📂</div>
            <p>Noch keine Klassenkameraden haben einen Ordner freigegeben.</p>
        </div>
    </div>
<?php else: ?>
    <?php foreach ($sharedUsers as $su): ?>
        <?php
        $ownerId    = (int)$su['id'];
        $ownerName  = $su['username'];
        $sharedPath = $su['shared_folder'];

        $resolvedDir = resolveSharedPath($ownerId, $sharedPath);
        $entries     = [];

        if ($resolvedDir !== null && is_dir($resolvedDir)) {
            // Nur direkte Dateien (keine Unterordner) auflisten
            foreach (new DirectoryIterator($resolvedDir) as $entry) {
                if ($entry->isFile() && !$entry->isDot()) {
                    $entries[] = $entry->getFilename();
                }
            }
            sort($entries);
        }
        ?>
        <div class="card">
            <h2>
                👤 <?= htmlspecialchars($ownerName) ?>
                <span class="shared-badge">Freigabe: <?= htmlspecialchars($sharedPath) ?></span>
            </h2>
            <?php if (empty($entries)): ?>
                <div class="empty-state" style="padding:1rem 0">
                    <p>Keine Dateien in diesem freigegebenen Ordner.</p>
                </div>
            <?php else: ?>
                <table class="file-table">
                    <thead>
                        <tr>
                            <th>Dateiname</th>
                            <th>Größe</th>
                            <th>Aktion</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($entries as $name): ?>
                            <?php
                            $fullPath = $resolvedDir . '/' . $name;
                            $size     = is_file($fullPath) ? filesize($fullPath) : 0;
                            ?>
                            <tr>
                                <td>📄 <?= htmlspecialchars($name) ?></td>
                                <td><?= htmlspecialchars(formatBytes((int)$size)) ?></td>
                                <td>
                                    <a href="/shared_download.php?owner=<?= $ownerId ?>&file=<?= urlencode($name) ?>"
                                       class="btn btn-primary btn-sm">⬇ Herunterladen</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php renderFooter(); ?>
