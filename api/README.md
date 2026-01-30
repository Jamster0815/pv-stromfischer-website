# 🔌 PV-Stromfischer Tarif API

REST API zum automatischen Lesen und Schreiben von Tarifen aus Ihrer Abrechnungssoftware.

## 🌐 Endpunkt

**Base URL**: `http://10.0.0.81/api/tarife.php`

**Zugriff**: Nur aus dem internen Netzwerk (10.0.0.0/24)

---

## 📖 API-Übersicht

### 1. Tarife abrufen (GET)

**Request:**
```http
GET /api/tarife.php HTTP/1.1
Host: 10.0.0.81
```

**Response (200 OK):**
```json
{
    "success": true,
    "data": {
        "bezugstarif": 11,
        "einspeisungstarif": 7,
        "mitgliedsbeitrag": 10,
        "letzteAktualisierung": "2025-12-17 15:30:00"
    }
}
```

---

### 2. Tarife aktualisieren (POST/PUT)

**Request:**
```http
POST /api/tarife.php HTTP/1.1
Host: 10.0.0.81
Content-Type: application/json

{
    "bezugstarif": 12,
    "einspeisungstarif": 8,
    "mitgliedsbeitrag": 15
}
```

**Response (200 OK):**
```json
{
    "success": true,
    "message": "Tarife erfolgreich aktualisiert",
    "data": {
        "bezugstarif": 12,
        "einspeisungstarif": 8,
        "mitgliedsbeitrag": 15,
        "letzteAktualisierung": "2025-12-17 15:35:00"
    }
}
```

---

## 💻 Code-Beispiele

### cURL (Kommandozeile)

#### Tarife abrufen:
```bash
curl http://10.0.0.81/api/tarife.php
```

#### Tarife aktualisieren:
```bash
curl -X POST http://10.0.0.81/api/tarife.php \
  -H "Content-Type: application/json" \
  -d '{
    "bezugstarif": 12,
    "einspeisungstarif": 8,
    "mitgliedsbeitrag": 15
  }'
```

---

### Python

```python
import requests
import json

# API URL
API_URL = "http://10.0.0.81/api/tarife.php"

# 1. Tarife abrufen
response = requests.get(API_URL)
tarife = response.json()

if tarife['success']:
    print(f"Bezugstarif: {tarife['data']['bezugstarif']} Cent/kWh")
    print(f"Einspeisungstarif: {tarife['data']['einspeisungstarif']} Cent/kWh")
    print(f"Mitgliedsbeitrag: {tarife['data']['mitgliedsbeitrag']} €")

# 2. Tarife aktualisieren
neue_tarife = {
    "bezugstarif": 12.5,
    "einspeisungstarif": 8.0,
    "mitgliedsbeitrag": 15.0
}

response = requests.post(
    API_URL,
    json=neue_tarife,
    headers={"Content-Type": "application/json"}
)

result = response.json()
if result['success']:
    print("✅ Tarife erfolgreich aktualisiert!")
    print(f"Letzte Aktualisierung: {result['data']['letzteAktualisierung']}")
else:
    print(f"❌ Fehler: {result['error']}")
```

---

### PHP

```php
<?php
// API URL
$api_url = 'http://10.0.0.81/api/tarife.php';

// 1. Tarife abrufen
$response = file_get_contents($api_url);
$tarife = json_decode($response, true);

if ($tarife['success']) {
    echo "Bezugstarif: " . $tarife['data']['bezugstarif'] . " Cent/kWh\n";
    echo "Einspeisungstarif: " . $tarife['data']['einspeisungstarif'] . " Cent/kWh\n";
    echo "Mitgliedsbeitrag: " . $tarife['data']['mitgliedsbeitrag'] . " €\n";
}

// 2. Tarife aktualisieren
$neue_tarife = [
    'bezugstarif' => 12.5,
    'einspeisungstarif' => 8.0,
    'mitgliedsbeitrag' => 15.0
];

$options = [
    'http' => [
        'method'  => 'POST',
        'header'  => 'Content-Type: application/json',
        'content' => json_encode($neue_tarife)
    ]
];

$context = stream_context_create($options);
$response = file_get_contents($api_url, false, $context);
$result = json_decode($response, true);

if ($result['success']) {
    echo "✅ Tarife erfolgreich aktualisiert!\n";
} else {
    echo "❌ Fehler: " . $result['error'] . "\n";
}
?>
```

---

### C# / .NET

```csharp
using System;
using System.Net.Http;
using System.Text;
using System.Text.Json;
using System.Threading.Tasks;

public class TarifAPI
{
    private static readonly string API_URL = "http://10.0.0.81/api/tarife.php";
    private static readonly HttpClient client = new HttpClient();

    // Tarife abrufen
    public static async Task<dynamic> GetTarife()
    {
        var response = await client.GetStringAsync(API_URL);
        return JsonSerializer.Deserialize<dynamic>(response);
    }

    // Tarife aktualisieren
    public static async Task<dynamic> UpdateTarife(
        double bezugstarif,
        double einspeisungstarif,
        double mitgliedsbeitrag)
    {
        var data = new
        {
            bezugstarif = bezugstarif,
            einspeisungstarif = einspeisungstarif,
            mitgliedsbeitrag = mitgliedsbeitrag
        };

        var json = JsonSerializer.Serialize(data);
        var content = new StringContent(json, Encoding.UTF8, "application/json");

        var response = await client.PostAsync(API_URL, content);
        var responseString = await response.Content.ReadAsStringAsync();

        return JsonSerializer.Deserialize<dynamic>(responseString);
    }

    // Beispiel-Verwendung
    public static async Task Main()
    {
        // Tarife abrufen
        var tarife = await GetTarife();
        Console.WriteLine($"Bezugstarif: {tarife.data.bezugstarif} Cent/kWh");

        // Tarife aktualisieren
        var result = await UpdateTarife(12.5, 8.0, 15.0);

        if (result.success)
        {
            Console.WriteLine("✅ Tarife erfolgreich aktualisiert!");
        }
        else
        {
            Console.WriteLine($"❌ Fehler: {result.error}");
        }
    }
}
```

---

### Node.js / JavaScript

```javascript
const axios = require('axios');

const API_URL = 'http://10.0.0.81/api/tarife.php';

// 1. Tarife abrufen
async function getTarife() {
    try {
        const response = await axios.get(API_URL);
        const tarife = response.data;

        if (tarife.success) {
            console.log(`Bezugstarif: ${tarife.data.bezugstarif} Cent/kWh`);
            console.log(`Einspeisungstarif: ${tarife.data.einspeisungstarif} Cent/kWh`);
            console.log(`Mitgliedsbeitrag: ${tarife.data.mitgliedsbeitrag} €`);
        }

        return tarife;
    } catch (error) {
        console.error('Fehler beim Abrufen:', error.message);
    }
}

// 2. Tarife aktualisieren
async function updateTarife(bezugstarif, einspeisungstarif, mitgliedsbeitrag) {
    try {
        const response = await axios.post(API_URL, {
            bezugstarif: bezugstarif,
            einspeisungstarif: einspeisungstarif,
            mitgliedsbeitrag: mitgliedsbeitrag
        }, {
            headers: { 'Content-Type': 'application/json' }
        });

        const result = response.data;

        if (result.success) {
            console.log('✅ Tarife erfolgreich aktualisiert!');
            console.log(`Letzte Aktualisierung: ${result.data.letzteAktualisierung}`);
        } else {
            console.error(`❌ Fehler: ${result.error}`);
        }

        return result;
    } catch (error) {
        console.error('Fehler beim Aktualisieren:', error.message);
    }
}

// Beispiel-Verwendung
(async () => {
    await getTarife();
    await updateTarife(12.5, 8.0, 15.0);
})();
```

---

## ⚠️ Fehlerbehandlung

### Fehlercodes

| Code | Bedeutung | Beschreibung |
|------|-----------|--------------|
| 200 | OK | Anfrage erfolgreich |
| 400 | Bad Request | Ungültige Parameter oder JSON |
| 404 | Not Found | Konfigurationsdatei nicht gefunden |
| 405 | Method Not Allowed | Ungültige HTTP-Methode |
| 500 | Internal Server Error | Serverfehler beim Speichern |

### Fehler-Response Beispiele

**Fehlende Parameter:**
```json
{
    "success": false,
    "error": "Fehlende Parameter. Erforderlich: bezugstarif, einspeisungstarif, mitgliedsbeitrag"
}
```

**Ungültige Werte:**
```json
{
    "success": false,
    "error": "Bezugstarif muss größer als 0 sein"
}
```

**Ungültiges JSON:**
```json
{
    "success": false,
    "error": "Ungültiges JSON-Format"
}
```

---

## 🔐 Sicherheit

- ✅ Nur aus internem Netzwerk (10.0.0.0/24) erreichbar
- ✅ Validierung aller Eingaben
- ✅ CORS-Header konfiguriert
- ⚠️ Keine Authentifizierung (da intern)

### Optional: API-Key hinzufügen

Wenn Sie zusätzliche Sicherheit wünschen, können Sie einen API-Key implementieren:

```php
// In tarife.php am Anfang hinzufügen:
$required_key = 'IhrGeheimesAPIKey123';
$provided_key = $_SERVER['HTTP_X_API_KEY'] ?? '';

if ($provided_key !== $required_key) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Ungültiger API-Key']);
    exit;
}
```

**Verwendung:**
```bash
curl -H "X-API-Key: IhrGeheimesAPIKey123" http://10.0.0.81/api/tarife.php
```

---

## 🧪 Testing

### Test-Script erstellen

Erstellen Sie `test_api.sh`:

```bash
#!/bin/bash
API_URL="http://10.0.0.81/api/tarife.php"

echo "=== Test 1: Tarife abrufen ==="
curl -s $API_URL | jq

echo -e "\n=== Test 2: Tarife aktualisieren ==="
curl -s -X POST $API_URL \
  -H "Content-Type: application/json" \
  -d '{
    "bezugstarif": 12,
    "einspeisungstarif": 8,
    "mitgliedsbeitrag": 15
  }' | jq

echo -e "\n=== Test 3: Prüfen ob aktualisiert ==="
curl -s $API_URL | jq
```

Ausführen:
```bash
chmod +x test_api.sh
./test_api.sh
```

---

## 📊 Integration mit Abrechnungssoftware

### Beispiel: Monatliche Tarifaktualisierung

```python
#!/usr/bin/env python3
"""
Automatische Tarifaktualisierung aus Abrechnungssoftware
Wird monatlich als Cron-Job ausgeführt
"""

import requests
import logging
from datetime import datetime

# Konfiguration
API_URL = "http://10.0.0.81/api/tarife.php"
LOG_FILE = "/var/log/tarif-update.log"

# Logging einrichten
logging.basicConfig(
    filename=LOG_FILE,
    level=logging.INFO,
    format='%(asctime)s - %(levelname)s - %(message)s'
)

def get_tarife_from_billing_system():
    """
    Hole aktuelle Tarife aus Ihrer Abrechnungssoftware
    TODO: An Ihre Software anpassen
    """
    # Beispiel - ersetzen Sie dies durch Ihre Logik
    return {
        "bezugstarif": 12.5,
        "einspeisungstarif": 8.0,
        "mitgliedsbeitrag": 15.0
    }

def update_website_tarife(tarife):
    """Aktualisiere Tarife auf der Website"""
    try:
        response = requests.post(
            API_URL,
            json=tarife,
            headers={"Content-Type": "application/json"},
            timeout=10
        )

        result = response.json()

        if result['success']:
            logging.info(f"Tarife erfolgreich aktualisiert: {tarife}")
            return True
        else:
            logging.error(f"API-Fehler: {result.get('error', 'Unbekannter Fehler')}")
            return False

    except requests.exceptions.RequestException as e:
        logging.error(f"Verbindungsfehler: {e}")
        return False
    except Exception as e:
        logging.error(f"Unerwarteter Fehler: {e}")
        return False

def main():
    logging.info("=== Tarifaktualisierung gestartet ===")

    # Tarife aus Abrechnungssoftware holen
    tarife = get_tarife_from_billing_system()
    logging.info(f"Tarife aus Abrechnungssystem: {tarife}")

    # Website aktualisieren
    success = update_website_tarife(tarife)

    if success:
        logging.info("=== Tarifaktualisierung erfolgreich ===")
    else:
        logging.error("=== Tarifaktualisierung fehlgeschlagen ===")

    return success

if __name__ == "__main__":
    main()
```

**Als Cron-Job (monatlich am 1. um 6:00 Uhr):**
```bash
crontab -e

# Tarife monatlich aktualisieren
0 6 1 * * /usr/bin/python3 /opt/scripts/update_tarife.py
```

---

## 📞 Support

Bei Fragen zur API-Integration:
- 📧 E-Mail: info@pv-stromfischer.at
- 📖 Dokumentation: `/var/www/html/api/README.md`

---

**Version**: 1.0
**Letzte Aktualisierung**: 2025-12-17
**Maintainer**: PV-Stromfischer Team
