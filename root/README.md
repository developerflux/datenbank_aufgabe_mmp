# MMP Lernplattform

Eine sichere, private Datenbankanwendung mit Webfrontend für gemeinsames Lernen und Arbeiten.

## Features

- **User-Authentifizierung:** Sicherer Login und Registrierung mit Passwort-Hashing (Bcrypt).
- **Persönlicher Speicherbereich:** Jeder User hat einen eigenen Bereich für Datei-Uploads.
- **Speicherlimit:** Automatisierte Prüfung des Speicherplatzes pro User.
- **Passwortverwaltung:** User können ihr Passwort ändern, wobei Sicherheitsregeln (min. 8 Zeichen) erzwungen werden.
- **Freigabe-System:** User können spezifische Pfade für andere User lesbar machen.
- **Softwareergonomisches Design:** Modernes, responsives Interface mit CSS.
- **Sicherheit:** Schutz vor SQL-Injection durch Prepared Statements und Zugriffskontrolle auf Dateiebene.

## Installation (USBWebserver)

1. Kopieren Sie den gesamten Ordner `datenbank_aufgabe_mmp` in das `root`-Verzeichnis Ihres USBWebservers.
2. Starten Sie den USBWebserver.
3. Öffnen Sie `phpMyAdmin` (meist unter `localhost:8080/phpmyadmin`).
4. Erstellen Sie eine neue Datenbank namens `mmp_learning_platform`.
5. Importieren Sie die Datei `database.sql` in diese Datenbank.
6. Die Anwendung ist nun unter `localhost:8080/datenbank_aufgabe_mmp/index.php` erreichbar.

## Projektstruktur

- `index.php`: Zentraler Einstiegspunkt (Routing).
- `config/`: Datenbankkonfiguration.
- `src/`: Anwendungscode (MVC-Struktur).
- `public/`: Statische Dateien (CSS, JS).
- `user_data/`: Speicherort für User-Dateien.

## Sicherheitshinweis

Stellen Sie sicher, dass der Ordner `user_data` vom Webserver beschreibbar ist. In einer Produktionsumgebung sollten zusätzliche Maßnahmen zur Absicherung des Datei-Uploads (z.B. MIME-Type Prüfung) implementiert werden.
