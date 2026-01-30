// Zentrale Tarif-Konfiguration für PV-Stromfischer
// Diese Datei wird von allen Seiten verwendet, um einheitliche Tarife zu gewährleisten

const PV_STROMFISCHER_TARIFE = {
    // EEG Tarife (in Cent/kWh, netto)
    bezugstarif: 11,        // Bezugstarif für Strom aus der EEG
    einspeisungstarif: 7,   // Einspeisungstarif für PV-Einspeisung

    // Mitgliedschaft
    mitgliedsbeitrag: 10,   // Euro pro Jahr (brutto)

    // Stand/Version
    stand: "2025",
    letzteAktualisierung: "2026-01-24 09:30:00"
};

// Für externe Verwendung verfügbar machen
if (typeof module !== 'undefined' && module.exports) {
    module.exports = PV_STROMFISCHER_TARIFE;
}
