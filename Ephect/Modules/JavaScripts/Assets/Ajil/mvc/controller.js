var Ajil = Ajil || {}

Ajil.MVC = Ajil.MVC || {}
Ajil.MVC.Controller = class _Controller extends Ajil.Web.Object {
    constructor(view, name) {
        super();
        this._domain = (view !== null) ? view.domain : '';
        this._isSecured = (view !== null) ? view.isSecured : false;
        this._hasView = true;
        if (view instanceof Ajil.MVC.View) {
            this._parent = view;
        }
        else if (typeof view === 'Object' || view === null) {
            throw new Error('Not a valid view');
        }
        else {
            this._hasView = false;
        }
        this._name = name;
    }
    oninit(callback) {
        if (typeof callback === 'function') {
            callback.call(this);
        }
        return this;
    }
    onload(callback) {
        var the = this;
        Ajil.DOM.ready(function () {
            if (typeof callback === 'function') {
                callback.call(the);
            }
        });
        return this;
    }
    render() {
        if (typeof this.oninit === 'function') {
            this.oninit();
        }
        if (typeof this.onload === 'function') {
            this.onload();
        }
    }
    actions(actions) {
        for (var key in actions) {
            this[key] = actions[key];
        }
        this.render();
        return this;
    }
    route(route, callback) {
        var routeMatcher = new RegExp(route.replace(/:[^\s/]+/g, '([\\w-]+)'));
        this._parent.requestView(view, action, args, callback);
    }
    getSimpleView(view, callback) {
        this._parent.requestSimpleView(view, callback);
    }
    getView(view, action, args, callback) {
        this._parent.requestView(view, action, args, callback);
    }
    getPartialView(pageName, action, attach, postData, callback) {
        this._parent.requestPart(pageName, action, attach, postData, callback);
    }
    parseViewResponse(pageName, callback) {
        this._parent.parseResponse(pageName, callback);
    }
    attachWindow(pageName, anchor) {
        this._parent.attachWindow(pageName, anchor);
    }
    attachView(pageName, anchor) {
        this._parent.attachView(pageName, anchor);
    }
    attachIframe(id, src, anchor) {
        this._parent.attachIframe(id, src, anchor);
    }
    static create(parent, name) {
        if (name === undefined) {
            name = 'ctrl' + Date.now();
        }
        return new Ajil.MVC.Controller(parent, name);
    }
}
