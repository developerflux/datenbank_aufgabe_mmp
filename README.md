# Klassen-Plattform

Eine private Webanwendung für eine Schulklasse, um gemeinsames Lernen, Arbeiten und Freizeitaktivitäten über das Internet zu organisieren.

## Funktionen

- **Benutzer-Authentifizierung**: Login/Registrierung mit gehashten Passwörtern (bcrypt)
- **Passwort-Regeln**: Min. 8 Zeichen, Groß-/Kleinbuchstaben, Zahl und Sonderzeichen
- **Passwort ändern**: Über eine gesicherte Eingabemaske
- **Persönlicher Speicherbereich**: Jeder User hat einen privaten Dateibereich (nur er/sie kann dort schreiben)
- **Speicherlimit**: Standard 50 MB pro User (konfigurierbar in der Datenbank)
- **Ordner-Freigabe**: User können einen Unterordner für lesenden Zugriff durch andere freigeben
- **Geschützte Bereiche**: Alle Seiten außer Login/Registrierung erfordern eine gültige Session

## Technologie-Stack

- **Backend**: PHP 8+ mit PDO (MySQL)
- **Datenbank**: MySQL / MariaDB
- **Frontend**: HTML5, CSS3 (kein Framework, eigene Stile)
- **Webserver**: Apache (mit `.htaccess`-Konfiguration)

## Einrichtung

### Voraussetzungen

- PHP 8.0 oder neuer
- MySQL 5.7 / MariaDB 10.3 oder neuer
- Apache mit `mod_rewrite` und `mod_headers`

### Installation

1. Repository in das Webroot-Verzeichnis kopieren (z.B. `/var/www/html/klassen`)

2. Datenbank anlegen:
   ```bash
   mysql -u root -p < sql/setup.sql
   ```

3. Datenbankverbindung in `config/database.php` anpassen:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'klassen_db');
   define('DB_USER', 'dein_db_user');
   define('DB_PASS', 'dein_passwort');
   ```

4. Schreibrechte für das `uploads/`-Verzeichnis setzen:
   ```bash
   chmod 750 uploads/
   chown www-data:www-data uploads/
   ```

5. Im Produktionsbetrieb in `config/database.php` die echten Datenbank-Zugangsdaten eintragen und die Datei außerhalb des Webroots ablegen oder durch serverseitige Umgebungsvariablen ersetzen.

## Verzeichnisstruktur

```
├── config/
│   └── database.php       # Datenbankverbindung
├── css/
│   └── style.css          # Stylesheet
├── includes/
│   ├── auth.php           # Authentifizierungs-Hilfsfunktionen
│   ├── header.php         # Gemeinsamer HTML-Kopf
│   └── footer.php         # Gemeinsamer HTML-Fuß
├── sql/
│   └── setup.sql          # Datenbank-Schema
├── uploads/               # Nutzer-Uploads (kein Webzugriff)
│   └── .htaccess          # Webzugriff sperren
├── .htaccess              # Apache-Konfiguration
├── index.php              # Login-Seite
├── register.php           # Registrierung
├── dashboard.php          # Persönlicher Bereich
├── upload.php             # Upload-Handler
├── download.php           # Download-Handler (privat)
├── delete.php             # Lösch-Handler
├── change_password.php    # Passwort ändern
├── set_shared.php         # Freigabe setzen
├── shared.php             # Geteilte Dateien anzeigen
├── shared_download.php    # Download-Handler (geteilte Dateien)
└── logout.php             # Abmelden
```

## Sicherheitshinweise

- Passwörter werden ausschließlich als bcrypt-Hashes gespeichert
- Alle Datenbankzugriffe verwenden PDO Prepared Statements (kein SQL Injection)
- Session-IDs werden nach Login neu generiert (Session Fixation Schutz)
- Direkter Webzugriff auf `uploads/` ist per `.htaccess` gesperrt
- Pfad-Traversal-Angriffe werden durch `realpath()`-Prüfungen verhindert
- Sicherheits-HTTP-Header werden gesetzt
- Verzeichnis-Listings sind deaktiviert
