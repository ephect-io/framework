var Ajil = Ajil || {}

Ajil.Backend = class _Backend {
    constructor() {
        this.isHacking = false;
        this.command = '';
    }
    static loadScriptsArray(scripts, callback) {

        var F = function (src) {
            if (Ajil.Registry.scriptExists(src)) {
                return false;
            }

            var tag = document.createElement("script");
            tag.src = src;
            tag.type = "text/javascript";

            tag.addEventListener('load', function (e) {
                // while (!e.returnValue) {

                // }

                if (scripts.length === 0 && typeof callback === 'function') {
                    callback.call(null);
                }
            })
            document.body.appendChild(tag);

            Ajil.Registry.script = src;

            if (scripts.length > 0) {
                let next = scripts.shift();

                if (next) {
                    F(next);
                }

            }

        };
        if (scripts.length > 0) {
            F(scripts.shift());
        }


    }

    static bindEvents() {

        window.onkeydown = function (e) {
            var code = e.keyCode ? e.keyCode : e.which;

            if (code === 27) { // ESC is typed
                this.command = '';
                console.log("command = '" + this.command + "'");
            }
        };

        window.onkeypress = function (e) {
            var code = e.keyCode ? e.keyCode : e.which;

            if (code === 35 && this.command === '') { // # is typed
                this.command += String.fromCharCode(code);
                console.log("command = '" + this.command + "'");
            } else if (code === 33 && this.command === '#') { // #! is typed
                this.command += String.fromCharCode(code);
                this.isHacking = true; // trying to log in as administrator
                console.log("command = '" + this.command + "'");
            } else if (this.isHacking) {
                this.command += String.fromCharCode(code);
                console.log("command = '" + this.command + "'");
            }

            Ajil.Commands.run(this.command);
        };
    }

}
