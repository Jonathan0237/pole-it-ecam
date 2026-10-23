// Appliqué avant l'affichage pour éviter un flash de thème.
(function () {
    try {
        var saved = localStorage.getItem('theme');
        if (saved === 'light' || saved === 'dark') {
            document.documentElement.setAttribute('data-theme', saved);
        }
    } catch (e) { /* stockage indisponible : thème du système */ }
})();
