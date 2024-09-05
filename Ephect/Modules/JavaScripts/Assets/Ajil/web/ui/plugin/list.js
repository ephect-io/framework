var Ajil = Ajil || {}
Ajil.Web = Ajil.Web || {}
Ajil.Web.UI = Ajil.Web.UI || {}

Ajil.Web.UI.List = class _List extends Ajil.Web.UI.Plugin {
    constructor() {
        super();
    }
    bind(container, data, callback) {
        let names = data.names;
        let values = data.values;
        let templates = data.templates;
        let elements = data.elements;
        let colNum = templates.length;
        let rowNum = values.length;
        let result = '';
        let html = '';
        let css = '';
        result = str_replace('%s', css, elements[0].opening) + "\n";
        let oldValue = [];
        for (let i = 0; i < rowNum; i++) {
            let row = (values[i] !== null) ? values[i] : Array.apply(null, Array(colNum)).map(String.prototype.valueOf, '&nbsp;');
            result += str_replace('%s', '', elements[1].opening) + "\n";
            for (let j = 0; j < colNum; j++) {
                let k = i * colNum + j;
                html = Ajil.Web.UI.Plugin.applyTemplate(templates, row, j);
                if (templates[j]['enabled'] == 1 && row[j] != oldValue[j]) {
                    result += str_replace('%s', '', elements[2].opening) + html + elements[2].closing + "\n";
                }
                oldValue[j] = row[j];
            }
            result += elements[1].closing + "\n";
        }
        result += elements[0].closing + "\n";
        document.querySelector(container).innerHTML = "&nbsp;";
        document.querySelector(container).innerHTML = result;
        if (typeof callback === 'function') {
            callback.call(this);
        }
    }
    static create() {
        return new Ajil.Web.UI.List();
    }
}
