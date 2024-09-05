var Ajil = Ajil || {}
Ajil.Web = Ajil.Web || {}
Ajil.Web.UI = Ajil.Web.UI || {}

Ajil.Web.UI.Accordion = class _Accordion extends Ajil.Web.UI.Plugin {
    constructor() {
        super();
    }
    bind(container, data, callback) {
        let names = data.names;
        let values = data.values;
        let templates = data.templates;
        let elements = data.elements;
        let templateNum = templates.length;
        let colNum = names.length;
        let rowNum = values.length;
        let result = '';
        let html = '';
        let level = 0;
        let index = 0;
        let canBind = 0;
        let bound = [false, false, false];
        let oldValues = Array.apply(null, Array(colNum)).map(String.prototype.valueOf, '!#');
        for (let k = 0; k < templateNum; k++) {
            for (let j = 0; j < colNum; j++) {
                if (templates[k].name === names[j]) {
                    templates[k].index = j;
                }
            }
        }
        for (let i = 0; i < rowNum; i++) {
            let row = (values[i] !== null) ? values[i] : Array.apply(null, Array(colNum)).map(String.prototype.valueOf, '&nbsp;');
            for (let j = 0; j < templateNum; j++) {
                if (j === 0) {
                    level = 0;
                }
                if (!templates[j].enabled)
                    continue;
                index = templates[j].index;
                canBind = row[index] !== oldValues[j];
                if (!canBind) {
                    bound[level] = canBind;
                    level++;
                    oldValues[j] = row[index];
                    continue;
                }
                //html = this.applyTemplate(templates[j], colNum, row, i);
                //html = row[index];
                html = Ajil.Web.UI.Plugin.applyTemplate(templates, row, j);
                if (level === 0) {
                    if (i > 0) {
                        result += elements[2].closing + elements[0].closing;
                        result += elements[2].closing + elements[0].closing;
                        oldValues = Array.apply(null, Array(colNum)).map(String.prototype.valueOf, '!#');
                    }
                    result += str_replace('%s', 'blue', elements[0].opening);
                    result += elements[1].opening + html + elements[1].closing;
                    result += elements[2].opening;
                }
                else if (level === 1) {
                    if (i > 0 && !bound[level - 1]) {
                        result += elements[2].closing + elements[0].closing;
                    }
                    else {
                    }
                    result += str_replace('%s', 'odd', elements[0].opening);
                    result += elements[1].opening + html + elements[1].closing;
                    result += elements[2].opening;
                }
                else if (level === 2) {
                    result += str_replace('%s', '', elements[2].opening) + html + elements[2].closing;
                }
                bound[level] = canBind;
                level++;
                oldValues[j] = row[index];
            }
        }
        result += elements[2].closing;
        result += elements[0].closing;
        result += elements[2].closing;
        result += elements[0].closing;
        document.querySelector(container).innerHTML = "&nbsp;";
        document.querySelector(container).innerHTML = result;
        if (typeof callback === 'function') {
            callback.call(this);
        }
    }
    static create() {
        return new Ajil.Web.UI.Accordion();
    }
}
