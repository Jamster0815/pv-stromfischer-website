# Tarif-Verwaltung PV-Stromfischer

## Preise ändern - ganz einfach!

Öffnen Sie die Datei: `/var/www/html/tarife-config.js`

Ändern Sie nur diese Werte:

```javascript
const PV_STROMFISCHER_TARIFE = {
    bezugstarif: 11,           // Cent/kWh - HIER ÄNDERN
    einspeisungstarif: 7,      // Cent/kWh - HIER ÄNDERN  
    mitgliedsbeitrag: 10       // Euro/Jahr - HIER ÄNDERN
};
```

## Was wird automatisch aktualisiert?

✅ Tarife-Seite (tarife.html)
✅ Ersparnis-Rechner (ersparnis-rechner.html)
✅ Alle Berechnungen
✅ Alle Anzeigen

## Beispiel

**Vorher:**
```javascript
bezugstarif: 11,
```

**Nachher:**
```javascript
bezugstarif: 12,
```

→ Speichern → Fertig! 🎉

Alle Seiten zeigen automatisch den neuen Preis von 12 Cent!
