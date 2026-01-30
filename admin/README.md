# 🔐 Admin-Panel - Tarif-Verwaltung

## Zugang

**URL**: https://pv-stromfischer.at/admin/

**Standard-Passwort**: `pvstromfischer2025`

⚠️ **WICHTIG**: Ändern Sie das Passwort in `/var/www/html/admin/index.php` Zeile 5!

## Funktionen

- ⚡ Bezugstarif ändern
- ☀️ Einspeisungstarif ändern
- 👥 Mitgliedsbeitrag ändern
- 💾 Sofortige Speicherung
- 👀 Vorschau der aktuellen Werte

## Passwort ändern

1. Datei öffnen: `/var/www/html/admin/index.php`
2. Zeile 5 finden:
   ```php
   $ADMIN_PASSWORD = 'pvstromfischer2025';
   ```
3. Neues Passwort eingeben:
   ```php
   $ADMIN_PASSWORD = 'IhrSicheresPasswort123!';
   ```
4. Speichern → Fertig!

## Sicherheitshinweise

- ✅ Verwenden Sie ein starkes Passwort
- ✅ Geben Sie das Passwort nicht weiter
- ✅ Loggen Sie sich nach Änderungen aus
- ✅ Verwenden Sie HTTPS (SSL-Zertifikat)
- ⚠️ Das Panel hat keinen Brute-Force-Schutz

## Zusätzlicher Schutz (Optional)

### IP-Whitelist mit .htaccess

Erstellen Sie `/var/www/html/admin/.htaccess`:

```apache
Order Deny,Allow
Deny from all
Allow from 192.168.1.100  # Ihre IP-Adresse
Allow from 10.0.0.0/8     # Lokales Netzwerk
```

### Zwei-Faktor-Authentifizierung

Für erweiterten Schutz können Sie Google Authenticator integrieren.

## Troubleshooting

### Fehler: "Fehler beim Speichern"

**Lösung**: Berechtigungen prüfen
```bash
sudo chmod 644 /var/www/html/tarife-config.js
sudo chown www-data:www-data /var/www/html/tarife-config.js
```

### Fehler: 403 Forbidden

**Lösung**: Admin-Verzeichnis-Berechtigungen
```bash
sudo chmod 755 /var/www/html/admin
sudo chmod 644 /var/www/html/admin/index.php
```

---

**Maintainer**: PV-Stromfischer Team
**Letzte Aktualisierung**: 2025-12-17
