<?php
// Admin-Panel - NUR FÜR INTERNES NETZWERK
// Kein Passwort nötig, da nur über interne IPs erreichbar

// Tarife speichern
$success = false;
$error = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_tarife'])) {
    $bezugstarif = floatval($_POST['bezugstarif']);
    $einspeisungstarif = floatval($_POST['einspeisungstarif']);
    $mitgliedsbeitrag = floatval($_POST['mitgliedsbeitrag']);

    // Validierung
    if ($bezugstarif <= 0 || $einspeisungstarif <= 0 || $mitgliedsbeitrag < 0) {
        $error = 'Bitte geben Sie gültige Werte ein!';
    } else {
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

        if (file_put_contents('../tarife-config.js', $config_content)) {
            $success = true;
        } else {
            $error = 'Fehler beim Speichern! Bitte Berechtigungen prüfen.';
        }
    }
}

// Aktuelle Tarife laden
$tarife_file = '../tarife-config.js';
$current_bezug = 11;
$current_einspeisung = 7;
$current_mitgliedsbeitrag = 10;
$last_update = '-';

if (file_exists($tarife_file)) {
    $content = file_get_contents($tarife_file);
    if (preg_match('/bezugstarif:\s*([0-9.]+)/', $content, $matches)) {
        $current_bezug = $matches[1];
    }
    if (preg_match('/einspeisungstarif:\s*([0-9.]+)/', $content, $matches)) {
        $current_einspeisung = $matches[1];
    }
    if (preg_match('/mitgliedsbeitrag:\s*([0-9.]+)/', $content, $matches)) {
        $current_mitgliedsbeitrag = $matches[1];
    }
    if (preg_match('/letzteAktualisierung:\s*"([^"]+)"/', $content, $matches)) {
        $last_update = $matches[1];
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tarif-Verwaltung - PV-Stromfischer</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #3d5a5c 0%, #4a6b6d 100%);
            min-height: 100vh;
            padding: 2rem;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            padding: 3rem;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .header {
            text-align: center;
            margin-bottom: 2rem;
            padding-bottom: 2rem;
            border-bottom: 3px solid #3d5a5c;
        }
        .header h1 {
            color: #3d5a5c;
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }
        .header .subtitle {
            color: #666;
            font-size: 1.1rem;
        }
        .network-info {
            background: #e7f3ff;
            border-left: 4px solid #2196F3;
            padding: 1rem;
            margin-bottom: 2rem;
            border-radius: 8px;
            font-size: 0.95rem;
        }
        .network-info strong {
            color: #1976D2;
        }
        .alert {
            padding: 1.25rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            font-weight: 500;
            animation: slideIn 0.5s ease;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 2px solid #c3e6cb;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 2px solid #f5c6cb;
        }
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .current-values {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 2rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            border: 2px solid #dee2e6;
        }
        .current-values h3 {
            color: #3d5a5c;
            margin-bottom: 1.5rem;
            font-size: 1.3rem;
        }
        .value-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
        }
        .value-item {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            text-align: center;
        }
        .value-icon {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        .value-label {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 0.5rem;
        }
        .value-amount {
            font-size: 1.8rem;
            font-weight: bold;
            color: #3d5a5c;
        }
        .value-unit {
            font-size: 0.9rem;
            color: #666;
        }
        .form-section {
            margin-bottom: 2rem;
        }
        .form-section h3 {
            color: #3d5a5c;
            margin-bottom: 1.5rem;
            font-size: 1.3rem;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 0.5rem;
        }
        .form-group {
            margin-bottom: 2rem;
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            border: 2px solid #e9ecef;
            transition: border-color 0.3s;
        }
        .form-group:hover {
            border-color: #3d5a5c;
        }
        label {
            display: block;
            margin-bottom: 0.8rem;
            color: #333;
            font-weight: 600;
            font-size: 1.1rem;
        }
        .label-icon {
            font-size: 1.3rem;
            margin-right: 0.5rem;
        }
        .label-sub {
            display: block;
            font-size: 0.9rem;
            color: #666;
            font-weight: normal;
            margin-top: 0.3rem;
        }
        input[type="number"] {
            width: 100%;
            padding: 1rem 1.5rem;
            border: 2px solid #ced4da;
            border-radius: 10px;
            font-size: 1.3rem;
            font-weight: bold;
            transition: all 0.3s;
            background: white;
        }
        input[type="number"]:focus {
            outline: none;
            border-color: #3d5a5c;
            box-shadow: 0 0 0 3px rgba(61, 90, 92, 0.1);
        }
        .input-helper {
            display: block;
            margin-top: 0.5rem;
            color: #6c757d;
            font-size: 0.9rem;
        }
        .btn-save {
            width: 100%;
            padding: 1.5rem 2rem;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1.3rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        }
        .btn-save:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4);
        }
        .btn-save:active {
            transform: translateY(0);
        }
        .footer-links {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 2px solid #e9ecef;
            text-align: center;
        }
        .footer-links a {
            color: #3d5a5c;
            text-decoration: none;
            font-weight: 500;
            margin: 0 1rem;
            transition: color 0.3s;
        }
        .footer-links a:hover {
            color: #f26522;
            text-decoration: underline;
        }
        .last-update {
            text-align: center;
            color: #6c757d;
            font-size: 0.9rem;
            margin-top: 1rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⚡ Tarif-Verwaltung</h1>
            <p class="subtitle">PV-Stromfischer Stadl-Paura</p>
        </div>

        <div class="network-info">
            <strong>🔒 Interner Zugang:</strong> Diese Seite ist nur über das interne Netzwerk erreichbar.
            Ihre IP: <strong><?= htmlspecialchars($_SERVER['REMOTE_ADDR']) ?></strong>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success">
                ✅ <strong>Erfolgreich gespeichert!</strong> Die Tarife wurden aktualisiert und sind sofort auf der Website sichtbar.
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-error">
                ❌ <strong>Fehler:</strong> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <div class="current-values">
            <h3>📊 Aktuelle Werte</h3>
            <div class="value-grid">
                <div class="value-item">
                    <div class="value-icon">⚡</div>
                    <div class="value-label">Bezugstarif</div>
                    <div class="value-amount"><?= number_format($current_bezug, 2, ',', '.') ?></div>
                    <div class="value-unit">Cent/kWh</div>
                </div>
                <div class="value-item">
                    <div class="value-icon">☀️</div>
                    <div class="value-label">Einspeisungstarif</div>
                    <div class="value-amount"><?= number_format($current_einspeisung, 2, ',', '.') ?></div>
                    <div class="value-unit">Cent/kWh</div>
                </div>
                <div class="value-item">
                    <div class="value-icon">👥</div>
                    <div class="value-label">Mitgliedsbeitrag</div>
                    <div class="value-amount"><?= number_format($current_mitgliedsbeitrag, 2, ',', '.') ?></div>
                    <div class="value-unit">€ / Jahr</div>
                </div>
            </div>
            <?php if ($last_update !== '-'): ?>
                <div class="last-update">
                    Letzte Aktualisierung: <?= htmlspecialchars($last_update) ?>
                </div>
            <?php endif; ?>
        </div>

        <form method="POST">
            <input type="hidden" name="save_tarife" value="1">

            <div class="form-section">
                <h3>✏️ Neue Werte eingeben</h3>

                <div class="form-group">
                    <label for="bezugstarif">
                        <span class="label-icon">⚡</span>
                        Bezugstarif
                        <span class="label-sub">Preis für Strom aus der Energiegemeinschaft</span>
                    </label>
                    <input type="number" step="0.01" id="bezugstarif" name="bezugstarif"
                           value="<?= $current_bezug ?>" required min="0">
                    <span class="input-helper">Cent pro kWh (z.B. 11.50)</span>
                </div>

                <div class="form-group">
                    <label for="einspeisungstarif">
                        <span class="label-icon">☀️</span>
                        Einspeisungstarif
                        <span class="label-sub">Vergütung für eingespeisten Solarstrom</span>
                    </label>
                    <input type="number" step="0.01" id="einspeisungstarif" name="einspeisungstarif"
                           value="<?= $current_einspeisung ?>" required min="0">
                    <span class="input-helper">Cent pro kWh (z.B. 7.50)</span>
                </div>

                <div class="form-group">
                    <label for="mitgliedsbeitrag">
                        <span class="label-icon">👥</span>
                        Mitgliedsbeitrag
                        <span class="label-sub">Jährliche Mitgliedsgebühr</span>
                    </label>
                    <input type="number" step="0.01" id="mitgliedsbeitrag" name="mitgliedsbeitrag"
                           value="<?= $current_mitgliedsbeitrag ?>" required min="0">
                    <span class="input-helper">Euro pro Jahr (z.B. 10.00)</span>
                </div>
            </div>

            <button type="submit" class="btn-save">
                💾 Tarife jetzt speichern
            </button>
        </form>

        <div class="footer-links">
            <p><strong>Vorschau der Änderungen:</strong></p>
            <a href="../tarife.html" target="_blank">→ Tarife-Seite</a>
            <a href="../ersparnis-rechner.html" target="_blank">→ Ersparnis-Rechner</a>
            <a href="../index.html" target="_blank">→ Homepage</a>
        </div>
    </div>
</body>
</html>
