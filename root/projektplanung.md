# Projektplanung: Datenbankanwendung mit Webfrontend für gemeinsames Lernen

## 1. Einleitung

Dieses Dokument beschreibt die Planung und Implementierung einer Datenbankanwendung mit Webfrontend, die es einem begrenzten Personenkreis innerhalb einer Klasse ermöglicht, gemeinsames Lernen, Arbeiten und Freizeitaktivitäten über das Internet zu organisieren. Die Anwendung wird selbst implementiert, um Sicherheits- und Datenschutzanforderungen gerecht zu werden.

## 2. Anforderungen

Die Anwendung muss folgende Kernanforderungen erfüllen:

*   **User-Authentifizierung:** User authentifizieren sich mit Name und Passwort.
*   **Persönlicher Speicherbereich:** Jeder User hat einen persönlichen Speicherbereich, in den nur er/sie über ein PHP-Formular schreiben kann.
*   **Softwareergonomisches User-Interface:** Das User-Interface muss benutzerfreundlich und intuitiv gestaltet sein.
*   **Gute Code-Qualität:** Redundanter Code soll vermieden und das Prinzip der "Separation of Concerns" konsequent eingehalten werden.
*   **Sicherheitsprüfung:** Geschützte Bereiche dürfen ohne die nötige Berechtigung nicht gelesen werden können.

### 2.1 Optionale Anforderungen

Folgende optionale Features sollen berücksichtigt werden:

*   **Passwort-Hashing:** Speicherung von kryptographischen Hashes der Passwörter.
*   **Passwortänderung:** User können ihr Passwort über eine Eingabemaske ändern. Ein PHP-Skript prüft die Einhaltung von Passwortregeln.
*   **Datenlimit:** Der persönliche Speicherbereich verfügt über ein Datenlimit pro User. Das Upload-Skript verhindert Uploads, die das Limit überschreiten.
*   **Freigabe-Funktion:** User können einen Pfad definieren, der einen Ordner zum Lesen an andere User freigibt.

## 3. Technologiestack

*   **Backend:** PHP
*   **Datenbank:** MySQL
*   **Frontend:** HTML, CSS, JavaScript (für UI/UX-Verbesserungen)
*   **Webserver:** USBWebserver (Apache/PHP/MySQL)

## 4. Projektstruktur

Die Projektstruktur wird wie folgt organisiert:

```
/datenbank_aufgabe_mmp
├── index.php
├── config/
│   └── db.php
├── public/
│   ├── css/
│   ├── js/
│   └── images/
├── src/
│   ├── controllers/
│   ├── models/
│   ├── views/
│   ├── includes/
│   └── utils/
├── user_data/
│   └── [username]/
└── projektplanung.md
```

*   `index.php`: Einstiegspunkt der Anwendung.
*   `config/`: Enthält Konfigurationsdateien, z.B. für die Datenbankverbindung.
*   `public/`: Statische Assets wie CSS, JavaScript und Bilder.
*   `src/`: Enthält den Haupt-Anwendungscode, aufgeteilt nach MVC-Prinzipien (Model, View, Controller).
    *   `controllers/`: Logik zur Verarbeitung von Anfragen.
    *   `models/`: Datenbankinteraktionen und Datenstrukturen.
    *   `views/`: HTML-Templates für die Darstellung.
    *   `includes/`: Wiederverwendbare PHP-Komponenten (z.B. Header, Footer).
    *   `utils/`: Hilfsfunktionen (z.B. für Passwort-Hashing, Validierung).
*   `user_data/`: Verzeichnis für die persönlichen Speicherbereiche der User.

## 5. Datenbankschema

Es werden mindestens zwei Tabellen benötigt:

### `users` Tabelle

Speichert User-Informationen.

| Feldname     | Datentyp           | Beschreibung                                 |
| :----------- | :----------------- | :------------------------------------------- |
| `id`         | INT (Primary Key)  | Eindeutige User-ID                           |
| `username`   | VARCHAR(255)       | User-Name (eindeutig)                        |
| `password`   | VARCHAR(255)       | Gehashtes Passwort                           |
| `storage_limit` | INT             | Optional: Speicherlimit in MB                |
| `shared_path` | VARCHAR(255)       | Optional: Pfad zum freigegebenen Ordner      |

### `files` Tabelle

Speichert Informationen zu den hochgeladenen Dateien.

| Feldname     | Datentyp           | Beschreibung                                 |
| :----------- | :----------------- | :------------------------------------------- |\n| `id`         | INT (Primary Key)  | Eindeutige Datei-ID                          |
| `user_id`    | INT                | Fremdschlüssel zur `users`-Tabelle           |
| `filename`   | VARCHAR(255)       | Originaler Dateiname                         |
| `filepath`   | VARCHAR(255)       | Pfad zur Datei auf dem Server                |
| `filesize`   | INT                | Dateigröße in Bytes                          |
| `upload_date`| DATETIME           | Upload-Datum und -Uhrzeit                    |

## 6. Implementierungsphasen

1.  **Repository klonen und Branch erstellen:** (Abgeschlossen)
2.  **Projektplanung als Markdown-Dokument erstellen:** (Aktuell)
3.  **Datenbankschema und Projektstruktur entwerfen:** Detaillierte Planung der Datenbanktabellen und Verzeichnisstruktur.
4.  **Core-Dateien implementieren:**
    *   `index.php` als zentraler Einstiegspunkt.
    *   Datenbankverbindung (`config/db.php`).
    *   Session-Management für User-Status.
5.  **Authentifizierungssystem implementieren:**
    *   Registrierungsformular und -logik.
    *   Login-Formular und -logik.
    *   Passwort-Hashing (z.B. `password_hash()` und `password_verify()`).
6.  **User-Dashboard und persönlichen Speicherbereich implementieren:**
    *   Anzeige der hochgeladenen Dateien.
    *   Upload-Formular und -Logik für Dateien.
    *   Sicherstellung, dass nur der User in seinen Bereich schreiben kann.
7.  **Optionale Features implementieren:**
    *   Passwortänderungsfunktion mit Regeln.
    *   Implementierung des Datenlimits beim Upload.
    *   Freigabe-Funktion für Ordner.
8.  **UI/UX-Verbesserungen und Code-Qualität sicherstellen:**
    *   Anwendung von CSS und JavaScript für ein softwareergonomisches Design.
    *   Refactoring und Kommentierung des Codes.
    *   Umfassende Sicherheitstests, insbesondere für geschützte Bereiche.
9.  **Alle Änderungen committen und pushen:** Hochladen des Codes in den `Niklas-Manus` Branch.
10. **Ergebnisse an den User liefern:** Bereitstellung der Anwendung und Dokumentation.

## 7. Sicherheitsaspekte

*   **Passwort-Hashing:** Verwendung starker Hashing-Algorithmen (z.B. `Bcrypt`).
*   **SQL-Injection-Schutz:** Verwendung von Prepared Statements.
*   **Cross-Site Scripting (XSS) Schutz:** Sanitisierung von User-Eingaben.
*   **Session-Hijacking-Schutz:** Sichere Session-Konfiguration (z.B. `httponly`, `secure` Flags).
*   **Zugriffskontrolle:** Strikte Überprüfung der Berechtigungen für Dateizugriffe und Speicherbereiche.

## 8. Teststrategie

*   **Unit-Tests:** Für einzelne Funktionen und Module (z.B. Passwort-Validierung).
*   **Integrationstests:** Überprüfung der Interaktion zwischen verschiedenen Komponenten (z.B. Login-Prozess).
*   **Sicherheitstests:** Gezielte Tests, um Schwachstellen wie SQL-Injections oder unautorisierte Zugriffe zu finden.
*   **Usability-Tests:** Überprüfung der Benutzerfreundlichkeit des Interfaces.

---

**Autor:** Manus AI
**Datum:** 06. Februar 2026
