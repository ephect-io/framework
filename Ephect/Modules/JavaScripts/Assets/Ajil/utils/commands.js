var Ajil = Ajil || {}

Ajil.Commands = (function () {

    class _Commands {
        constructor() {}
        clearRuntime(callback) {
            Ajil.ajax('admin/console/', {
                "action": 'clearRuntime'
            }, function (data, status, xhr) {
                if (typeof callback == 'function') {
                    callback.call(this, data, status, xhr);
                } else {
                    console.log(data.result);
                }
            });
        }
        run(command) {
            if (command === '#!r') { // rlog command
                this.clearRuntime();
            }
        }
    }

    return new _Commands();
})();