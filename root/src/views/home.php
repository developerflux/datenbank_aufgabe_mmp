<div style="text-align: center; padding: 3rem 0;">
    <h1>Willkommen auf der MMP Lernplattform</h1>
    <p style="font-size: 1.2rem; margin-bottom: 2rem;">
        Organisiere dein Lernen, Arbeiten und deine Freizeitaktivitäten in einem sicheren, privaten Umfeld.
    </p>
    <?php if (!isset($_SESSION['user_id'])): ?>
        <div style="display: flex; justify-content: center; gap: 1rem;">
            <a href="index.php?page=login" class="button" style="background: var(--primary-color); color: white; padding: 0.7rem 2rem; text-decoration: none; border-radius: 4px;">Login</a>
            <a href="index.php?page=register" class="button" style="background: var(--secondary-color); color: white; padding: 0.7rem 2rem; text-decoration: none; border-radius: 4px;">Registrieren</a>
        </div>
    <?php else: ?>
        <a href="index.php?page=dashboard" class="button" style="background: var(--primary-color); color: white; padding: 0.7rem 2rem; text-decoration: none; border-radius: 4px;">Zum Dashboard</a>
    <?php endif; ?>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; margin-top: 3rem;">
    <div style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h3>Privatsphäre zuerst</h3>
        <p>Keine vorgefertigten Produkte großer Anbieter. Deine Daten gehören dir.</p>
    </div>
    <div style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h3>Persönlicher Speicher</h3>
        <p>Jeder User erhält einen eigenen Bereich für Dokumente und Dateien.</p>
    </div>
    <div style="background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h3>Gemeinsames Arbeiten</h3>
        <p>Teile Inhalte mit deiner Klasse und organisiere Freizeitaktivitäten.</p>
    </div>
</div>
