<?php
/**
 * PV-Stromfischer Anmeldeformular - E-Mail Versand mit PHPMailer
 */

require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// ============================================
// KONFIGURATION - Bitte anpassen!
// ============================================

$smtp_host = 'mail.pv-stromfischer.at';  // oder smtp.pv-stromfischer.at
$smtp_port = 465;
$smtp_user = 'office@pv-stromfischer.at';
$smtp_pass = 'PV-Stromfischer-Office';  // Passwort hier eintragen falls nötig
$smtp_secure = 'ssl';  // 'tls' für Port 587, 'ssl' für Port 465

$empfaenger = 'office@pv-stromfischer.at';
$absender_email = 'info@pv-stromfischer.at';
$absender_name = 'PV-Stromfischer Formular';

// ============================================
// AB HIER NICHTS ÄNDERN
// ============================================

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// TEST-MODUS
if (isset($_GET['test'])) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = $smtp_host;
        $mail->Port = $smtp_port;
        $mail->SMTPDebug = 0;
        
        if (!empty($smtp_pass)) {
            $mail->SMTPAuth = true;
            $mail->Username = $smtp_user;
            $mail->Password = $smtp_pass;
        } else {
            $mail->SMTPAuth = false;
        }
        
        if ($smtp_secure) {
            $mail->SMTPSecure = $smtp_secure;
        }
        
        echo json_encode([
            'status' => 'PHPMailer geladen!',
            'smtp_host' => $smtp_host,
            'smtp_port' => $smtp_port,
            'smtp_user' => $smtp_user,
            'smtp_auth' => !empty($smtp_pass) ? 'Ja' : 'Nein (ohne Passwort)',
            'php_version' => PHP_VERSION
        ], JSON_PRETTY_PRINT);
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'Fehler',
            'message' => $e->getMessage()
        ]);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Nur POST erlaubt. Testen mit ?test=1']);
    exit;
}

// JSON-Daten empfangen
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Ungültige Daten']);
    exit;
}

// Pflichtfelder prüfen
$pflichtfelder = ['name', 'email', 'phone', 'address', 'plz', 'city', 'supplyEnergy', 'consumeEnergy', 'dataProtection', 'emailContact'];
foreach ($pflichtfelder as $feld) {
    if (empty($data[$feld])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => "Pflichtfeld fehlt: $feld"]);
        exit;
    }
}

// Mitgliedschaftsart übersetzen
$mitgliedschaft_labels = [
    'private' => 'Privat',
    'taxExempt' => 'Unecht steuerbefreite Organisation (Verein, Kleinunternehmer, usw.)',
    'company' => 'Unternehmen (mit Vorsteuerabzugsberechtigung)',
    'farmer' => 'Landwirt (mit Vorsteuerabzugsberechtigung)',
    'other' => 'Andere: ' . ($data['otherMembership'] ?? '')
];
$mitgliedschaft = $mitgliedschaft_labels[$data['membershipType']] ?? $data['membershipType'];

// HTML E-Mail erstellen
$html_content = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; }
        h1 { color: #1a5f5a; border-bottom: 2px solid #1a5f5a; padding-bottom: 10px; }
        h2 { color: #1a5f5a; margin-top: 25px; font-size: 18px; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        td { padding: 8px; border-bottom: 1px solid #ddd; }
        td:first-child { font-weight: bold; width: 40%; color: #666; }
        .highlight { background: #f0f9f8; }
        .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; font-size: 12px; color: #888; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎣 Neue Anmeldung PV-Stromfischer</h1>
        
        <h2>1. Persönliche Informationen</h2>
        <table>
            <tr><td>Name:</td><td>' . htmlspecialchars($data['name']) . '</td></tr>
            <tr><td>Geburtsdatum:</td><td>' . htmlspecialchars($data['birthdate'] ?? '-') . '</td></tr>
            <tr><td>Adresse:</td><td>' . htmlspecialchars($data['address']) . '</td></tr>
            <tr><td>PLZ / Ort:</td><td>' . htmlspecialchars($data['plz']) . ' ' . htmlspecialchars($data['city']) . '</td></tr>
            <tr><td>Abw. Anlagenadresse:</td><td>' . htmlspecialchars($data['altAddress'] ?? '-') . '</td></tr>
            <tr><td>E-Mail:</td><td><a href="mailto:' . htmlspecialchars($data['email']) . '">' . htmlspecialchars($data['email']) . '</a></td></tr>
            <tr><td>Telefon:</td><td>' . htmlspecialchars($data['phone']) . '</td></tr>
        </table>
        
        <h2>2. Mitgliedschaft</h2>
        <table>
            <tr class="highlight"><td>Art:</td><td><strong>' . htmlspecialchars($mitgliedschaft) . '</strong></td></tr>
        </table>
        
        <h2>3. Energieoptionen</h2>
        <table>';

// Energie liefern
if (($data['supplyEnergy'] ?? 'no') === 'yes') {
    $html_content .= '
            <tr class="highlight"><td colspan="2"><strong>✅ Möchte Energie LIEFERN</strong></td></tr>
            <tr><td>Zählpunkt:</td><td>' . htmlspecialchars($data['supplyMeterPoint'] ?? '-') . '</td></tr>
            <tr><td>Zählerinventarnr.:</td><td>' . htmlspecialchars($data['supplyMeterInventory'] ?? '-') . '</td></tr>
            <tr><td>PV Leistung:</td><td>' . htmlspecialchars($data['pvPower'] ?? '-') . ' kWp</td></tr>
            <tr><td>Max. Einspeiseleistung:</td><td>' . htmlspecialchars($data['maxFeedIn'] ?? '-') . ' kW</td></tr>';
} else {
    $html_content .= '
            <tr><td colspan="2">❌ Möchte NICHT liefern</td></tr>';
}

// Energie beziehen
if (($data['consumeEnergy'] ?? 'no') === 'yes') {
    $html_content .= '
            <tr class="highlight"><td colspan="2"><strong>✅ Möchte Energie BEZIEHEN</strong></td></tr>
            <tr><td>Zählpunkt:</td><td>' . htmlspecialchars($data['consumeMeterPoint'] ?? '-') . '</td></tr>
            <tr><td>Zählerinventarnr.:</td><td>' . htmlspecialchars($data['consumeMeterInventory'] ?? '-') . '</td></tr>';
} else {
    $html_content .= '
            <tr><td colspan="2">❌ Möchte NICHT beziehen</td></tr>';
}

$html_content .= '
        </table>
        
        <h2>4. Zustimmung & Unterschrift</h2>
        <table>
            <tr><td>Ort, Datum:</td><td>' . htmlspecialchars($data['signaturePlace'] ?? '-') . ', ' . htmlspecialchars($data['signatureDate'] ?? '-') . '</td></tr>
            <tr><td>Zustimmung erteilt:</td><td>✅ Ja</td></tr>
        </table>
        
        <p><strong>Unterschrift:</strong> Siehe Anhang</p>
        
        <div class="footer">
            Diese Anmeldung wurde über das Online-Formular auf www.pv-stromfischer.at gesendet.<br>
            Zeitpunkt: ' . date('d.m.Y H:i:s') . ' Uhr
        </div>
    </div>
</body>
</html>';

// PHPMailer konfigurieren und senden
$mail = new PHPMailer(true);

try {
    // SMTP Einstellungen
    $mail->isSMTP();
    $mail->Host = $smtp_host;
    $mail->Port = $smtp_port;
    $mail->CharSet = 'UTF-8';
    
    if (!empty($smtp_pass)) {
        $mail->SMTPAuth = true;
        $mail->Username = $smtp_user;
        $mail->Password = $smtp_pass;
    } else {
        $mail->SMTPAuth = false;
    }
    
    if ($smtp_secure) {
        $mail->SMTPSecure = $smtp_secure;
    }
    
    // Absender und Empfänger
    $mail->setFrom($absender_email, $absender_name);
    $mail->addAddress($empfaenger);
    $mail->addReplyTo($data['email'], $data['name']);
    
    // Inhalt
    $mail->isHTML(true);
    $mail->Subject = '[PV-Stromfischer] Neue Anmeldung: ' . $data['name'];
    $mail->Body = $html_content;
    
    // Unterschrift als Anhang
    if (!empty($data['signature']) && strpos($data['signature'], 'base64,') !== false) {
        $signature_parts = explode('base64,', $data['signature']);
        $signature_data = base64_decode($signature_parts[1]);
        $mail->addStringAttachment($signature_data, 'unterschrift.png', 'base64', 'image/png');
    }
    
    $mail->send();
    
    // Bestätigungsmail an Absender
    $bestaetigung = new PHPMailer(true);
    $bestaetigung->isSMTP();
    $bestaetigung->Host = $smtp_host;
    $bestaetigung->Port = $smtp_port;
    $bestaetigung->CharSet = 'UTF-8';
    
    if (!empty($smtp_pass)) {
        $bestaetigung->SMTPAuth = true;
        $bestaetigung->Username = $smtp_user;
        $bestaetigung->Password = $smtp_pass;
    } else {
        $bestaetigung->SMTPAuth = false;
    }
    
    if ($smtp_secure) {
        $bestaetigung->SMTPSecure = $smtp_secure;
    }
    
    $bestaetigung->setFrom($absender_email, $absender_name);
    $bestaetigung->addAddress($data['email'], $data['name']);
    $bestaetigung->isHTML(false);
    $bestaetigung->Subject = 'Ihre Anmeldung bei PV-Stromfischer';
    $bestaetigung->Body = "Sehr geehrte/r " . $data['name'] . ",\n\n" .
        "vielen Dank für Ihre Anmeldung bei der Energiegemeinschaft PV-Stromfischer Stadl-Paura.\n\n" .
        "Wir haben Ihre Anmeldung erhalten und werden uns in Kürze bei Ihnen melden.\n\n" .
        "Mit sonnigen Grüßen,\n" .
        "Ihr PV-Stromfischer Team\n\n" .
        "---\n" .
        "www.pv-stromfischer.at\n" .
        "office@pv-stromfischer.at";
    
    $bestaetigung->send();
    
    echo json_encode(['success' => true, 'message' => 'Anmeldung erfolgreich gesendet']);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'E-Mail Fehler: ' . $mail->ErrorInfo
    ]);
}
?>