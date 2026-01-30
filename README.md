# PV-Stromfischer Website

Offizielle Website der Energiegemeinschaft PV-Stromfischer Stadl-Paura.

## 🌐 Live-Website

- **Homepage**: https://pv-stromfischer.at
- **Live Dashboard**: https://pv-stromfischer.at/dashboard.html
- **Tarife**: https://pv-stromfischer.at/tarife.html
- **Ersparnis-Rechner**: https://pv-stromfischer.at/ersparnis-rechner.html
- **Anmeldung**: https://pv-stromfischer.at/anmeldung/

## 📋 Über das Projekt

Die PV-Stromfischer Website informiert über die Energiegemeinschaft in Stadl-Paura und bietet:

- 📊 **Live Dashboard** - Echtzeit-Daten der Solaranlagen
- 💰 **Ersparnis-Rechner** - Berechnung der individuellen Ersparnis
- ⚡ **Tarif-Übersicht** - Aktuelle Preise für Bezug und Einspeisung
- 📝 **Anmeldeformular** - Interessensbekundung für neue Mitglieder

## 🔧 Technologie-Stack

- **Frontend**: HTML5, CSS3, Vanilla JavaScript
- **Charts**: Chart.js
- **Backend**: PHP (für Formulare und InfluxDB-Proxy)
- **Datenbank**: InfluxDB (für Live-Daten)
- **Server**: Apache/Nginx

## 📁 Projektstruktur

```
/var/www/html/
├── index.html                  # Homepage
├── dashboard.html              # Live Dashboard mit Chart.js
├── tarife.html                 # Tarif-Übersicht
├── ersparnis-rechner.html      # Ersparnis-Kalkulator
├── tarife-config.js            # ⚠️ ZENTRALE TARIF-KONFIGURATION
├── influx-proxy.php            # Proxy für InfluxDB-Daten
├── anmeldung/
│   ├── index.html              # Anmeldeformular
│   └── formular-absenden.php   # Formular-Backend
├── PV-Stromfischer.png         # Logo
├── Stadl-Paura.jpg             # Hero-Image
└── README.md                   # Diese Datei
```

## ⚙️ Tarife ändern

**🎯 WICHTIG**: Alle Tarife werden zentral in einer Datei verwaltet!

### Schnellanleitung

1. Datei öffnen: `/var/www/html/tarife-config.js`
2. Werte ändern:
   ```javascript
   const PV_STROMFISCHER_TARIFE = {
       bezugstarif: 11,           // Cent/kWh
       einspeisungstarif: 7,      // Cent/kWh
       mitgliedsbeitrag: 10       // Euro/Jahr
   };
   ```
3. Speichern → Fertig!

**Automatisch aktualisiert werden:**
- ✅ Tarife-Seite
- ✅ Ersparnis-Rechner
- ✅ Alle Berechnungen

➡️ **Detaillierte Anleitung**: Siehe [TARIFE-AENDERN.md](TARIFE-AENDERN.md)

## 🚀 Installation / Deployment

### Voraussetzungen

- Webserver (Apache/Nginx)
- PHP 7.4+ mit mail() Funktion
- InfluxDB (optional, für Live-Dashboard)

### Setup-Schritte

```bash
# 1. Repository klonen
git clone https://github.com/your-org/pv-stromfischer-website.git
cd pv-stromfischer-website

# 2. Dateien nach /var/www/html kopieren
sudo cp -r * /var/www/html/

# 3. Berechtigungen setzen
sudo chown -R www-data:www-data /var/www/html
sudo chmod 644 /var/www/html/*.html
sudo chmod 644 /var/www/html/*.js
sudo chmod 755 /var/www/html/anmeldung

# 4. InfluxDB-Verbindung konfigurieren (optional)
# In influx-proxy.php die Verbindungsdaten anpassen:
# $influxUrl = 'http://10.0.0.81:8086';
# $database = 'iobroker';
```

## 📧 Kontaktformular konfigurieren

Passen Sie in `/var/www/html/anmeldung/formular-absenden.php` die E-Mail-Adresse an:

```php
$empfaenger = "info@pv-stromfischer.at";  // Ihre E-Mail-Adresse
```

## 🎨 Design & Branding

### Farben

```css
--primary: #3d5a5c      /* Hauptfarbe - Grün */
--accent: #f26522       /* Akzentfarbe - Orange */
--construction: #f59e0b /* Banner - Orange */
```

### Logo ändern

Ersetzen Sie `/var/www/html/PV-Stromfischer.png` mit Ihrem Logo (empfohlen: transparenter Hintergrund, PNG, min. 200x200px)

## 📊 Live Dashboard

Das Dashboard zeigt Echtzeit-Daten aus InfluxDB:

**Konfiguration** in `dashboard.html`:
```javascript
const PROXY_URL = './influx-proxy.php';
```

**Benötigte Messungen** in InfluxDB:
- `Gesamtleistung` (Watt) - Aktuelle Leistung der Anlagen

## 🔐 Sicherheit

- ✅ PHP-Formular mit Input-Validierung
- ✅ CORS-Header konfiguriert
- ✅ Keine SQL-Injection möglich (InfluxDB-Queries sind vordefiniert)
- ⚠️ Empfehlung: HTTPS/SSL-Zertifikat verwenden

## 🐛 Troubleshooting

### Ersparnis-Rechner funktioniert nicht

**Problem**: tarife-config.js kann nicht geladen werden

**Lösung**:
```bash
sudo chmod 644 /var/www/html/tarife-config.js
sudo chown www-data:www-data /var/www/html/tarife-config.js
```

### Dashboard zeigt keine Daten

**Problem**: InfluxDB nicht erreichbar

**Lösung**:
1. Prüfen Sie die InfluxDB-URL in `influx-proxy.php`
2. Testen Sie: `curl http://10.0.0.81:8086/ping`

### Formular sendet keine E-Mails

**Problem**: PHP mail() nicht konfiguriert

**Lösung**: SMTP konfigurieren oder externen Mail-Service nutzen

## 📝 Lizenz

© 2025 PV-Stromfischer Stadl-Paura

---

## 👥 Kontakt

**PV-Stromfischer**
- 📧 E-Mail: info@pv-stromfischer.at
- 🌐 Website: https://pv-stromfischer.at
- 📍 Standort: Stadl-Paura, Österreich

## 🤝 Beitragen

Haben Sie Verbesserungsvorschläge? Erstellen Sie ein Issue oder Pull Request!

1. Fork das Repository
2. Erstellen Sie einen Feature Branch (`git checkout -b feature/NeuesFunktion`)
3. Committen Sie Ihre Änderungen (`git commit -m 'Neue Funktion hinzugefügt'`)
4. Push zum Branch (`git push origin feature/NeuesFunktion`)
5. Erstellen Sie einen Pull Request

---

⚡ Powered by renewable energy from Stadl-Paura
