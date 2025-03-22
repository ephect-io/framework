var Ajil = Ajil || {};

Ajil.Object = class _Object {
    constructor(parent = null) {
        this._id = '';
        this._name = '';
        this._parent = parent;
    }

    get id() {
        return this._id;
    }

    get name() {
        return this._name;
    }

    get parent() {
        return this._parent;
    }
}
