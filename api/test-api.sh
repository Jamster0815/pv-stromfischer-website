#!/bin/bash
# Test-Script für PV-Stromfischer Tarif API

API_URL="http://localhost/api/tarife.php"

echo "========================================"
echo "  PV-Stromfischer API Test"
echo "========================================"
echo ""

# Test 1: GET - Tarife abrufen
echo "=== Test 1: Tarife abrufen (GET) ==="
echo "curl $API_URL"
echo ""
curl -s $API_URL | python3 -m json.tool
echo ""
echo ""

# Test 2: POST - Tarife aktualisieren
echo "=== Test 2: Tarife aktualisieren (POST) ==="
echo 'Neue Werte: Bezug=12, Einspeisung=8, Mitgliedsbeitrag=15'
echo ""
curl -s -X POST $API_URL \
  -H "Content-Type: application/json" \
  -d '{
    "bezugstarif": 12,
    "einspeisungstarif": 8,
    "mitgliedsbeitrag": 15
  }' | python3 -m json.tool
echo ""
echo ""

# Test 3: Prüfen ob die Änderung übernommen wurde
echo "=== Test 3: Prüfen ob aktualisiert (GET) ==="
echo ""
curl -s $API_URL | python3 -m json.tool
echo ""
echo ""

# Test 4: Zurücksetzen auf Original-Werte
echo "=== Test 4: Zurücksetzen auf Original-Werte ==="
echo 'Zurück auf: Bezug=11, Einspeisung=7, Mitgliedsbeitrag=10'
echo ""
curl -s -X POST $API_URL \
  -H "Content-Type: application/json" \
  -d '{
    "bezugstarif": 11,
    "einspeisungstarif": 7,
    "mitgliedsbeitrag": 10
  }' | python3 -m json.tool
echo ""
echo ""

# Test 5: Fehlertest - Fehlende Parameter
echo "=== Test 5: Fehlertest - Fehlende Parameter ==="
echo ""
curl -s -X POST $API_URL \
  -H "Content-Type: application/json" \
  -d '{
    "bezugstarif": 12
  }' | python3 -m json.tool
echo ""
echo ""

# Test 6: Fehlertest - Ungültige Werte
echo "=== Test 6: Fehlertest - Ungültiger Wert (negativ) ==="
echo ""
curl -s -X POST $API_URL \
  -H "Content-Type: application/json" \
  -d '{
    "bezugstarif": -5,
    "einspeisungstarif": 7,
    "mitgliedsbeitrag": 10
  }' | python3 -m json.tool
echo ""
echo ""

echo "========================================"
echo "  Tests abgeschlossen!"
echo "========================================"
