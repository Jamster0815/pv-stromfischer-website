# 💰 Tarife ändern - Schritt-für-Schritt Anleitung

## 🎯 Überblick

Alle Tarife der PV-Stromfischer Website werden **zentral in einer einzigen Datei** verwaltet. Wenn Sie die Preise dort ändern, werden sie automatisch auf der gesamten Website aktualisiert.

## 📍 Wo sind die Tarife gespeichert?

**Datei**: `/var/www/html/tarife-config.js`

Diese Datei enthält:
- Bezugstarif (Preis für Strom aus der EEG)
- Einspeisungstarif (Vergütung für eingespeisten Strom)
- Mitgliedsbeitrag (jährliche Gebühr)

## 🔧 Anleitung: Tarife ändern

### Option 1: Per SSH / Terminal (empfohlen)

```bash
# 1. Mit Server verbinden
ssh user@pv-stromfischer.at

# 2. Datei bearbeiten
sudo nano /var/www/html/tarife-config.js

# 3. Werte ändern (siehe Beispiel unten)

# 4. Speichern mit Strg+O, Enter, Strg+X

# 5. Berechtigungen prüfen (falls nötig)
sudo chmod 644 /var/www/html/tarife-config.js
sudo chown www-data:www-data /var/www/html/tarife-config.js
```

### Option 2: Per FTP / SFTP

1. Mit FTP-Client verbinden (z.B. FileZilla)
2. Navigieren zu `/var/www/html/`
3. Datei `tarife-config.js` herunterladen
4. Mit Texteditor öffnen
5. Werte ändern
6. Datei wieder hochladen
7. ✅ Fertig!

### Option 3: Per Git (für Entwickler)

```bash
# 1. Repository klonen
git clone https://github.com/your-org/pv-stromfischer-website.git
cd pv-stromfischer-website

# 2. Datei bearbeiten
nano tarife-config.js

# 3. Änderungen committen
git add tarife-config.js
git commit -m "Tarife aktualisiert: Bezug 12 Cent, Einspeisung 8 Cent"
git push origin main

# 4. Auf Server deployen
ssh user@server.com "cd /var/www/html && git pull"
```

## 📝 Die Datei im Detail

```javascript
// Zentrale Tarif-Konfiguration für PV-Stromfischer
// Diese Datei wird von allen Seiten verwendet

const PV_STROMFISCHER_TARIFE = {
    // EEG Tarife (in Cent/kWh, netto)
    bezugstarif: 11,        // ← HIER ÄNDERN
    einspeisungstarif: 7,   // ← HIER ÄNDERN

    // Mitgliedschaft
    mitgliedsbeitrag: 10,   // ← HIER ÄNDERN (Euro pro Jahr)

    // Stand/Version
    stand: "2025",
    letzteAktualisierung: "2025-01-01"  // ← Optional anpassen
};
```

### Was bedeuten die Werte?

| Feld | Bedeutung | Einheit | Beispiel |
|------|-----------|---------|----------|
| `bezugstarif` | Preis für Strom aus der EEG | Cent/kWh | 11 |
| `einspeisungstarif` | Vergütung für eingespeisten Strom | Cent/kWh | 7 |
| `mitgliedsbeitrag` | Jährliche Mitgliedsgebühr | Euro/Jahr | 10 |

## ✅ Was wird automatisch aktualisiert?

Nach dem Speichern der Datei werden die neuen Preise **sofort** auf allen Seiten angezeigt:

### 1. Tarife-Seite (tarife.html)
- ⚡ Bezugstarif-Karte zeigt neuen Preis
- ☀️ Einspeisungstarif-Karte zeigt neuen Preis

### 2. Ersparnis-Rechner (ersparnis-rechner.html)
- 🧮 Alle Berechnungen verwenden neue Preise
- 📊 Kostenaufschlüsselung zeigt neue Werte
- 💡 Info-Text "EEG-Bezugstarif: XX Cent/kWh" wird aktualisiert

### 3. Alle Berechnungen
- Ersparnis-Vergleich "Ohne EEG vs. Mit EEG"
- Jahreskosten-Kalkulation
- ROI-Berechnungen

## 🔍 Beispiel: Preiserhöhung

### Vorher
```javascript
bezugstarif: 11,           // 11 Cent/kWh
einspeisungstarif: 7,      // 7 Cent/kWh
```

### Nachher
```javascript
bezugstarif: 12,           // 12 Cent/kWh (+ 1 Cent)
einspeisungstarif: 8,      // 8 Cent/kWh (+ 1 Cent)
```

### Ergebnis
- Tarife-Seite zeigt: "12 Cent/kWh"
- Ersparnis-Rechner berechnet mit 12 Cent
- Keine weiteren Änderungen nötig! ✅

## 🔄 Änderungen testen

1. **Datei speichern**
2. **Browser öffnen** → https://pv-stromfischer.at/tarife.html
3. **Strg+F5** drücken (Hard Refresh)
4. **Prüfen**: Werden die neuen Preise angezeigt?

### Wenn die Änderungen nicht sichtbar sind:

```bash
# Browser-Cache leeren
Strg + Shift + R (Windows/Linux)
Cmd + Shift + R (Mac)

# Oder Inkognito-Modus testen

# Server-Cache prüfen (falls vorhanden)
sudo systemctl restart apache2  # Apache
sudo systemctl restart nginx    # Nginx
```

## ⚠️ Wichtige Hinweise

### ✅ DO's
- ✅ Nur Zahlen eingeben (keine Buchstaben)
- ✅ Cent-Beträge ohne Komma (z.B. `11` nicht `11,00`)
- ✅ Nach Änderungen testen
- ✅ Git-Commit mit aussagekräftiger Message
- ✅ Backup vor größeren Änderungen

### ❌ DON'Ts
- ❌ Keine Sonderzeichen (außer Punkt)
- ❌ Nicht die Dateistruktur ändern
- ❌ Nicht die Variablennamen ändern
- ❌ Nicht die JavaScript-Syntax zerstören

## 🛡️ Sicherheit & Berechtigungen

Die Datei benötigt folgende Berechtigungen:

```bash
# Prüfen
ls -la /var/www/html/tarife-config.js
# Sollte sein: -rw-r--r-- 1 www-data www-data

# Falls falsch, korrigieren:
sudo chmod 644 /var/www/html/tarife-config.js
sudo chown www-data:www-data /var/www/html/tarife-config.js
```

**Bedeutung**:
- `644` = Besitzer kann lesen/schreiben, alle anderen nur lesen
- `www-data` = Webserver kann die Datei laden

## 📊 Versions-Historie (Beispiel)

Dokumentieren Sie Änderungen in Git:

```bash
# Beispiel Git-Commits
git commit -m "Tarife 2025: Bezug 11 Cent, Einspeisung 7 Cent"
git commit -m "Mitgliedsbeitrag erhöht auf 15 Euro"
git commit -m "Q2 2025: Preisanpassung Bezug auf 12 Cent"
```

## 🆘 Troubleshooting

### Problem: Ersparnis-Rechner zeigt "undefined"

**Ursache**: JavaScript kann tarife-config.js nicht laden

**Lösung**:
```bash
# 1. Datei-Berechtigungen prüfen
sudo chmod 644 /var/www/html/tarife-config.js
sudo chown www-data:www-data /var/www/html/tarife-config.js

# 2. JavaScript-Syntax prüfen
cat /var/www/html/tarife-config.js

# 3. Browser-Console öffnen (F12) und nach Fehlern suchen
```

### Problem: Alte Preise werden noch angezeigt

**Ursache**: Browser-Cache

**Lösung**:
1. Hard Refresh: `Strg + F5`
2. Cache leeren: Browser-Einstellungen → Cache löschen
3. Inkognito-Modus testen

### Problem: Änderungen gehen bei Git Pull verloren

**Ursache**: Lokale Änderungen werden überschrieben

**Lösung**:
```bash
# Änderungen vorher committen
git add tarife-config.js
git commit -m "Tarife aktualisiert"
git push
```

## 📞 Support

Bei Problemen:

1. **Check Browser-Console** (F12 → Console)
2. **Check Server-Logs**: `/var/log/apache2/error.log`
3. **Check Datei-Berechtigungen**: `ls -la tarife-config.js`
4. **Kontakt**: info@pv-stromfischer.at

---

## 📚 Weitere Dokumentation

- [README.md](README.md) - Projekt-Übersicht
- [DEPLOYMENT.md](DEPLOYMENT.md) - Deployment-Anleitung
- [CONTRIBUTING.md](CONTRIBUTING.md) - Contribution Guidelines

---

**Letzte Aktualisierung**: 2025-12-17
**Maintainer**: PV-Stromfischer Team
