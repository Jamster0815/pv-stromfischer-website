<?php
/**
 * PV-Stromfischer Tarif API
 *
 * Erlaubt das Lesen und Schreiben von Tarifen via REST API
 * Nur aus dem internen Netzwerk erreichbar (10.0.0.0/24)
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// OPTIONS Request für CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Pfad zur Tarif-Konfiguration
$CONFIG_FILE = '../tarife-config.js';

/**
 * Aktuelle Tarife auslesen
 */
function getTarife() {
    global $CONFIG_FILE;

    if (!file_exists($CONFIG_FILE)) {
        return [
            'success' => false,
            'error' => 'Konfigurationsdatei nicht gefunden',
            'code' => 404
        ];
    }

    $content = file_get_contents($CONFIG_FILE);

    // Werte aus JavaScript-Datei extrahieren
    $tarife = [
        'bezugstarif' => 11,
        'einspeisungstarif' => 7,
        'mitgliedsbeitrag' => 10
    ];

    if (preg_match('/bezugstarif:\s*([0-9.]+)/', $content, $matches)) {
        $tarife['bezugstarif'] = floatval($matches[1]);
    }
    if (preg_match('/einspeisungstarif:\s*([0-9.]+)/', $content, $matches)) {
        $tarife['einspeisungstarif'] = floatval($matches[1]);
    }
    if (preg_match('/mitgliedsbeitrag:\s*([0-9.]+)/', $content, $matches)) {
        $tarife['mitgliedsbeitrag'] = floatval($matches[1]);
    }
    if (preg_match('/letzteAktualisierung:\s*"([^"]+)"/', $content, $matches)) {
        $tarife['letzteAktualisierung'] = $matches[1];
    } else {
        $tarife['letzteAktualisierung'] = null;
    }

    return [
        'success' => true,
        'data' => $tarife,
        'code' => 200
    ];
}

/**
 * Tarife aktualisieren
 */
function updateTarife($data) {
    global $CONFIG_FILE;

    // Validierung
    if (!isset($data['bezugstarif']) || !isset($data['einspeisungstarif']) || !isset($data['mitgliedsbeitrag'])) {
        return [
            'success' => false,
            'error' => 'Fehlende Parameter. Erforderlich: bezugstarif, einspeisungstarif, mitgliedsbeitrag',
            'code' => 400
        ];
    }

    $bezugstarif = floatval($data['bezugstarif']);
    $einspeisungstarif = floatval($data['einspeisungstarif']);
    $mitgliedsbeitrag = floatval($data['mitgliedsbeitrag']);

    // Validierung der Werte
    if ($bezugstarif <= 0) {
        return [
            'success' => false,
            'error' => 'Bezugstarif muss größer als 0 sein',
            'code' => 400
        ];
    }
    if ($einspeisungstarif <= 0) {
        return [
            'success' => false,
            'error' => 'Einspeisungstarif muss größer als 0 sein',
            'code' => 400
        ];
    }
    if ($mitgliedsbeitrag < 0) {
        return [
            'success' => false,
            'error' => 'Mitgliedsbeitrag darf nicht negativ sein',
            'code' => 400
        ];
    }

    // Neue Konfiguration erstellen
    $config_content = "// Zentrale Tarif-Konfiguration für PV-Stromfischer\n";
    $config_content .= "// Diese Datei wird von allen Seiten verwendet, um einheitliche Tarife zu gewährleisten\n\n";
    $config_content .= "const PV_STROMFISCHER_TARIFE = {\n";
    $config_content .= "    // EEG Tarife (in Cent/kWh, netto)\n";
    $config_content .= "    bezugstarif: $bezugstarif,        // Bezugstarif für Strom aus der EEG\n";
    $config_content .= "    einspeisungstarif: $einspeisungstarif,   // Einspeisungstarif für PV-Einspeisung\n\n";
    $config_content .= "    // Mitgliedschaft\n";
    $config_content .= "    mitgliedsbeitrag: $mitgliedsbeitrag,   // Euro pro Jahr (brutto)\n\n";
    $config_content .= "    // Stand/Version\n";
    $config_content .= "    stand: \"2025\",\n";
    $config_content .= "    letzteAktualisierung: \"" . date('Y-m-d H:i:s') . "\"\n";
    $config_content .= "};\n\n";
    $config_content .= "// Für externe Verwendung verfügbar machen\n";
    $config_content .= "if (typeof module !== 'undefined' && module.exports) {\n";
    $config_content .= "    module.exports = PV_STROMFISCHER_TARIFE;\n";
    $config_content .= "}\n";

    // Speichern
    if (file_put_contents($CONFIG_FILE, $config_content)) {
        return [
            'success' => true,
            'message' => 'Tarife erfolgreich aktualisiert',
            'data' => [
                'bezugstarif' => $bezugstarif,
                'einspeisungstarif' => $einspeisungstarif,
                'mitgliedsbeitrag' => $mitgliedsbeitrag,
                'letzteAktualisierung' => date('Y-m-d H:i:s')
            ],
            'code' => 200
        ];
    } else {
        return [
            'success' => false,
            'error' => 'Fehler beim Speichern der Konfiguration. Bitte Berechtigungen prüfen.',
            'code' => 500
        ];
    }
}

// Request-Methode auswerten
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Tarife abrufen
        $result = getTarife();
        http_response_code($result['code']);
        unset($result['code']);
        echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        break;

    case 'POST':
    case 'PUT':
        // Tarife aktualisieren
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        if ($data === null) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => 'Ungültiges JSON-Format'
            ], JSON_PRETTY_PRINT);
            break;
        }

        $result = updateTarife($data);
        http_response_code($result['code']);
        unset($result['code']);
        echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        break;

    default:
        http_response_code(405);
        echo json_encode([
            'success' => false,
            'error' => 'Methode nicht erlaubt. Erlaubt: GET, POST, PUT'
        ], JSON_PRETTY_PRINT);
        break;
}
?>
