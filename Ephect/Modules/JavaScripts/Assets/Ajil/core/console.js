var Ajil = Ajil || {};

Ajil.Console = (function () {
  class _Console {
    constructor() {
      this._log = [];
    }
    log() {
      var arr = [];
      for (var i = 0; i < arguments.length; i++) {
        arr.push(arguments[i]);
      }
      this._log.push(arr.join(", "));
    }
    trace() {
      var stack;
      try {
        throw new Error();
      }
      catch (ex) {
        stack = ex.stack;
      }
      this.log("console.trace()\n" + stack.split("\n").slice(2).join("  \n"));
    }
    dir(obj) {
      this.log("Content of " + obj);
      for (var key in obj) {
        var value = typeof obj[key] === "function" ? "function" : obj[key];
        this.log(" -\"" + key + "\" -> \"" + value + "\"");
      }
    }
    show() {
      alert(this._log.join("\n"));
      this._log = [];
    }
  }

  return new _Console();
})();

window.onerror = function (msg, url, line) {
  Ajil.Console.log("ERROR: \"" + msg + "\" at \"" + "\", line " + line);
}

if ((/android/gi).test(navigator.appVersion)) {
  window.addEventListener("touchstart", function (e) {
    if (e.touches.length === 3) {
      Ajil.Console.show();
    }
  });
}
