# 🚀 API Quick Start - Tarife aus Abrechnungssoftware ändern

## ✅ Die API ist fertig und getestet!

**API-Endpunkt**: `http://10.0.0.81/api/tarife.php`

---

## 📖 Schnellstart

### 1️⃣ Tarife abrufen

```bash
curl http://10.0.0.81/api/tarife.php
```

**Antwort:**
```json
{
    "success": true,
    "data": {
        "bezugstarif": 11,
        "einspeisungstarif": 7,
        "mitgliedsbeitrag": 10,
        "letzteAktualisierung": "2025-12-18 14:00:54"
    }
}
```

---

### 2️⃣ Tarife ändern

```bash
curl -X POST http://10.0.0.81/api/tarife.php \
  -H "Content-Type: application/json" \
  -d '{
    "bezugstarif": 12,
    "einspeisungstarif": 8,
    "mitgliedsbeitrag": 15
  }'
```

**Antwort:**
```json
{
    "success": true,
    "message": "Tarife erfolgreich aktualisiert",
    "data": {
        "bezugstarif": 12,
        "einspeisungstarif": 8,
        "mitgliedsbeitrag": 15,
        "letzteAktualisierung": "2025-12-18 14:00:53"
    }
}
```

---

## 💻 Integration in Ihre Abrechnungssoftware

### Python Beispiel

```python
import requests

# Tarife aus Ihrer Abrechnungssoftware
neue_tarife = {
    "bezugstarif": 12.5,
    "einspeisungstarif": 8.0,
    "mitgliedsbeitrag": 15.0
}

# An Website senden
response = requests.post(
    "http://10.0.0.81/api/tarife.php",
    json=neue_tarife
)

if response.json()['success']:
    print("✅ Tarife auf Website aktualisiert!")
```

### C# Beispiel

```csharp
using System.Net.Http;
using System.Text;
using System.Text.Json;

var data = new {
    bezugstarif = 12.5,
    einspeisungstarif = 8.0,
    mitgliedsbeitrag = 15.0
};

var json = JsonSerializer.Serialize(data);
var content = new StringContent(json, Encoding.UTF8, "application/json");

var client = new HttpClient();
var response = await client.PostAsync(
    "http://10.0.0.81/api/tarife.php",
    content
);

var result = await response.Content.ReadAsStringAsync();
Console.WriteLine(result);
```

---

## 🔐 Sicherheit

- ✅ Nur aus internem Netzwerk erreichbar (10.0.0.0/24)
- ✅ Automatische Validierung aller Werte
- ✅ Fehlerbehandlung

---

## 📊 Was wird automatisch aktualisiert?

Nach erfolgreicher API-Anfrage werden **SOFORT** aktualisiert:

1. ✅ `/var/www/html/tarife-config.js` (zentrale Konfiguration)
2. ✅ Tarife-Seite (`tarife.html`)
3. ✅ Ersparnis-Rechner (`ersparnis-rechner.html`)
4. ✅ Alle Berechnungen

→ **Kein Neustart, keine Verzögerung!**

---

## 🧪 API testen

```bash
# Test-Script ausführen
/var/www/html/api/test-api.sh

# Oder manuell testen
curl http://10.0.0.81/api/tarife.php
```

---

## 📚 Vollständige Dokumentation

Siehe: `/var/www/html/api/README.md`

- Alle HTTP-Methoden
- Code-Beispiele in 6 Programmiersprachen
- Fehlerbehandlung
- Integration mit Abrechnungssoftware

---

## 🆘 Support

**Fragen?** → info@pv-stromfischer.at

---

**API Version**: 1.0
**Status**: ✅ Produktiv
**Getestet**: 2025-12-18
