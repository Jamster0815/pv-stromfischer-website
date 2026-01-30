<?php
/**
 * PV-Stromfischer Kontaktformular - E-Mail Versand mit PHPMailer
 */

require 'anmeldung/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// ============================================
// SMTP KONFIGURATION
// ============================================

$smtp_host = 'mail.pv-stromfischer.at';
$smtp_port = 465;
$smtp_user = 'office@pv-stromfischer.at';
$smtp_pass = 'PV-Stromfischer-Office';
$smtp_secure = 'ssl';

$empfaenger = 'Info@pv-stromfischer.at';
$absender_email = 'info@pv-stromfischer.at';
$absender_name = 'PV-Stromfischer Kontaktformular';

// ============================================

header('Content-Type: application/json; charset=utf-8');

// Nur POST-Anfragen erlauben
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Ungültige Anfrage']);
    exit;
}

// Eingaben validieren und bereinigen
$name = isset($_POST['name']) ? trim(strip_tags($_POST['name'])) : '';
$email = isset($_POST['email']) ? trim(strip_tags($_POST['email'])) : '';
$phone = isset($_POST['phone']) ? trim(strip_tags($_POST['phone'])) : '';
$subject = isset($_POST['subject']) ? trim(strip_tags($_POST['subject'])) : '';
$message = isset($_POST['message']) ? trim(strip_tags($_POST['message'])) : '';
$datenschutz = isset($_POST['datenschutz']) ? true : false;

// Pflichtfelder prüfen
if (empty($name) || empty($email) || empty($subject) || empty($message) || !$datenschutz) {
    echo json_encode(['success' => false, 'message' => 'Bitte füllen Sie alle Pflichtfelder aus.']);
    exit;
}

// E-Mail validieren
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Bitte geben Sie eine gültige E-Mail-Adresse an.']);
    exit;
}

// HTML E-Mail für PV-Stromfischer erstellen
$html_content = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        h1 { color: #3d5a5c; border-bottom: 2px solid #f26522; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        td { padding: 10px; border-bottom: 1px solid #ddd; }
        td:first-child { font-weight: bold; width: 30%; color: #666; }
        .message-box { background: #f8f9fa; padding: 15px; border-left: 4px solid #3d5a5c; margin: 20px 0; }
        .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; font-size: 12px; color: #888; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📧 Neue Kontaktanfrage</h1>

        <table>
            <tr><td>Name:</td><td>' . htmlspecialchars($name) . '</td></tr>
            <tr><td>E-Mail:</td><td><a href="mailto:' . htmlspecialchars($email) . '">' . htmlspecialchars($email) . '</a></td></tr>';

if (!empty($phone)) {
    $html_content .= '<tr><td>Telefon:</td><td>' . htmlspecialchars($phone) . '</td></tr>';
}

$html_content .= '
            <tr><td>Betreff:</td><td><strong>' . htmlspecialchars($subject) . '</strong></td></tr>
        </table>

        <div class="message-box">
            <strong>Nachricht:</strong><br><br>
            ' . nl2br(htmlspecialchars($message)) . '
        </div>

        <div class="footer">
            Diese Nachricht wurde über das Kontaktformular auf www.pv-stromfischer.at gesendet.<br>
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
    $mail->addReplyTo($email, $name);

    // Inhalt
    $mail->isHTML(true);
    $mail->Subject = '[Kontaktformular] ' . $subject;
    $mail->Body = $html_content;

    // Alternative Nur-Text-Version
    $mail->AltBody = "Neue Kontaktanfrage\n\n" .
                     "Name: $name\n" .
                     "E-Mail: $email\n" .
                     ($phone ? "Telefon: $phone\n" : "") .
                     "Betreff: $subject\n\n" .
                     "Nachricht:\n$message\n\n" .
                     "---\nGesendet über www.pv-stromfischer.at am " . date('d.m.Y H:i:s');

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

    $bestaetigung->setFrom($absender_email, 'PV-Stromfischer');
    $bestaetigung->addAddress($email, $name);
    $bestaetigung->isHTML(false);
    $bestaetigung->Subject = 'Bestätigung Ihrer Kontaktanfrage - PV-Stromfischer';
    $bestaetigung->Body = "Sehr geehrte/r $name,\n\n" .
        "vielen Dank für Ihre Kontaktanfrage!\n\n" .
        "Wir haben folgende Nachricht von Ihnen erhalten:\n\n" .
        "Betreff: $subject\n" .
        "Nachricht:\n$message\n\n" .
        "Wir werden Ihre Anfrage schnellstmöglich bearbeiten und uns bei Ihnen melden.\n\n" .
        "Mit freundlichen Grüßen\n" .
        "Ihr Team der PV-Stromfischer\n" .
        "Energiegemeinschaft Stadl-Paura\n\n" .
        "---\n" .
        "Verein PV-Stromfischer\n" .
        "Schwanenstädter Straße 40\n" .
        "4651 Stadl-Hausruck\n" .
        "E-Mail: Info@pv-stromfischer.at\n" .
        "Web: www.pv-stromfischer.at";

    $bestaetigung->send();

    echo json_encode([
        'success' => true,
        'message' => 'Vielen Dank für Ihre Nachricht! Wir haben Ihnen eine Bestätigung per E-Mail gesendet und werden uns schnellstmöglich bei Ihnen melden.'
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Beim Versenden der Nachricht ist ein Fehler aufgetreten. Bitte versuchen Sie es erneut oder schreiben Sie uns direkt an Info@pv-stromfischer.at'
    ]);
}
?>
