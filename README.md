Website für Bits & Bäume | Augsburg München

Basierend auf Symfony 6 und Bootstrap

## Develop

- Install (Lando)[https://lando.dev] on your local machine
- `git clone`
- `lando npm install`
- `lando npm run watch` (Start webpack watcher to automatically compile SCSS and JS)
- `lando start` (Start an HTTP webserver, URL is displayed)

## Wichtige Verzeichnisse + Dateien

- `.env`: Enthält alle Umgebungsvariablen die gesetzt werden müssen, damit die Website läuft. Am besten
  eine `.env.local` erstellen und in dieser Datei andere Werte eintragen. Die `.env.local` überschreibt die
  normale `.env` Datei und wird auch nicht committed. Alternativ können die Umgebungsvariablen auch ganz normal ohne
  Datei gesetzt werden (bei Docker zum Beispiel).
- `assets`: Enthält CSS und Javascript für diese Website. Es wird automatisch kompiliert und landet dann im `public`
  Verzeichnis.
- `templates`: Enthält die Twig templates aus denen das HTML generiert wird.
- `src/Controller`: Enthält PHP Controller. Diese Controller definieren, welche Seiten es gibt, welches Template dort
  gerendert werden soll und welche Variablen das Template übergeben bekommt.
- `public`: Der HTTP-Server muss auf diesen Ordner zeigen. Es ist der Einstiegspunkt der Website. Alle Dateien darin
  sind öffentlich aufrufbar
- `public/events.json`: Eine maschinenlesbare Datei, die alle Veranstaltungen von Bits & Bäume enthält. Die
  Veranstaltungen können dort händisch im JSON+LD Format eingetragen werden. Daraus generiert sich die Website. Außerdem
  dient die Datei als Basis für automatisierte Benachrichtigungen und co. Wichtig ist dass jede Veranstaltung ein @id
  Feld hat, das eindeutig ist.

## Production build

- `npm run-script build`
