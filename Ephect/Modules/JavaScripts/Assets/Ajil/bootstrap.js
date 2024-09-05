
Ajil.DOM.ready(function () {

    Ajil.Backend.loadScriptsArray(Ajil.DOM.depends, function () {
        Ajil.Backend.loadScriptsArray(Ajil.DOM.sources, function() {
            if (typeof window[Ajil.DOM.main] === 'function') {
                var initnow = 'ajil_app_init_' + Date.now();
                window[initnow] = window[Ajil.DOM.main];
                window[Ajil.DOM.main] = null;
                window[initnow]();
            }
        });
    });
    Ajil.Backend.bindEvents();
});