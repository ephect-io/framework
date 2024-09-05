var Ajil = Ajil || {}

Ajil.Registry = (function () {
    
    class _Registry {
        constructor() {
            this._registry = [];
        }
        write(item, key, value) {
            if (this._registry[item] === undefined) {
                this._registry[item] = {};
            }
            this._registry[item][key] = value;
        }
        read(item, key, defaultValue) {
            var result = null;
            if (this._registry[item] !== undefined) {
                result = (this._registry[item][key] !== undefined) ? this._registry[item][key] : ((defaultValue !== undefined) ? defaultValue : null);
            }
            return result;
        }
        item(item) {
            if(item == '') {
                item = '#';
            }

            if (item === null || item === undefined) {
                return null;
            }

            if (this._registry[item] !== undefined && this._registry[item] !== null) {
                return this._registry[item];
            }
            else {
                this._registry[item] = {};
                return this._registry[item];
            }
        }
        items() {
            return this._registry;
        }
        clear() {
            this._registry = {};
        }
        exists(item, key, value) {
            if(item !== undefined && key === undefined && value === undefined) {
                return this._registry[item] !== undefined;
            }
            if(item !== undefined && key !== undefined && value === undefined) {
                return this._registry[item][key] !== undefined;
            }
            if(item !== undefined && key !== undefined && value !== undefined) {
                return this._registry[item][key][value] !== undefined;
            }
        }
        set token(value) {
            this._registry['token'] = value;
            return this;
        }
        get token() {
            return this._registry['token'];
        }
        set origin(value) {
            this._registry['origin'] = value;
            return this;
        }
        get origin() {
            return this._registry['origin'];
        }
        set script(script) {
            var s = script.replace(/\//g, '_');
            this.write('scripts', s, script);
        }
        set scripts(scripts) {
            for(var i in scripts) {
                var value = scripts[i];
                var s = value.replace(/\//g, '_');

                this.write('scripts', s, value);
            }
        }
        get scripts() {
            return this.read('scripts');
        }
        scriptExists(script) {
            var s = script.replace(/\//g, '_');
            if(this._registry['scripts'] === undefined) {
                this._registry['scripts'] = [];
            }
            return this._registry['scripts'][s] === script;

        }
    }

    return new _Registry();
})();
