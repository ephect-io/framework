var Ajil = Ajil || {}
Ajil.Web = Ajil.Web || {}
Ajil.Web.UI = Ajil.Web.UI || {}

Ajil.Web.UI.Table = class _Table extends Ajil.Web.UI.Plugin {
    constructor() {
        super();
    }
    fill(tableId, data, callback) {
        let values = data.values;
        let templates = data.templates;
        let colNum = templates.length;
        let rowNum = values.length;
        for (let j = 0; j < rowNum; j++) {
            let row = values[j];
            for (let i = 0; i < colNum; i++) {
                let template = templates[i];
                let html = Ajil.Web.UI.Plugin.applyTemplate(templates, row, i);
                if (template.enabled) {
                    document.querySelector(tableId + 'td' + (i + colNum * j).toString()).innerHTML = html;
                }
            }
        }
        if (typeof callback === 'function') {
            callback.call(this);
        }
    }
    bind(tableId, data, callback) {
        // let names = data.names;
        let head = data.names;
        let values = data.values;
        let templates = data.templates;
        let elements = data.elements;
        let colNum = head.length;
        let rowNum = values.length;
        let colIndex = 0;
        let noTHead = false;

        let result = "\n";
        let typeId1 = '';
        if(templates[0].name != '*') {
            colNum = templates.length;
        }

        result += str_replace('%s', 'id="' + tableId + elements[0].type + '" class="table table-striped table-hover table-condensed"', elements[0].opening) + "\n";

        var i = 0;
        let thead = elements[1].opening + "\n";
        let typeId0 = 'id="' + tableId + elements[3].type + (0) + '"';
        thead += str_replace('%s', typeId0, elements[3].opening) + "\n";
        for (let j = 0; j < colNum; j++) {
            let colName = head[j];
            if(templates[0].name != '*') {
                colName = templates[j].name;
            }
            colIndex = array_keys(head, colName)[0];
            typeId1 = 'id="' + tableId + elements[4].type + j + '"';
            thead += str_replace('%s', typeId1, elements[4].opening) + head[colIndex] + elements[4].closing + "\n";
        }

        thead += elements[3].closing + "\n";
        thead += elements[1].closing + "\n";

        let tbody = elements[2].opening + "\n";
        // let body = values;
        for (let i = 0; i < rowNum; i++) {

            let row = (values[i] !== null) ? values[i] : Array.apply(null, Array(colNum)).map(String.prototype.valueOf, '&nbsp;');

            typeId0 = 'id="' + tableId + elements[3].type + (i) + '"';
            tbody += str_replace('%s', typeId0, elements[3].opening) + "\n";

            for (let j = 0; j < colNum; j++) {
                let k = i * colNum + j;
                let colIndex = 0;
                let dataIndex = array_keys(head, head[j])[0];
                if(templates[0].name != '*') {
                    dataIndex = array_keys(head, templates[j]['name'])[0];
                    colIndex = dataIndex;
                }

                noTHead = templates[j] !== undefined && templates[j]['content'] != '' && templates[j]['enabled'] == 1;

                let html = row[dataIndex];
                if (noTHead) {
                    html = Ajil.Web.UI.Plugin.applyTemplate(templates, row, j);
                }
                
                if (templates[colIndex] !== undefined && templates[colIndex]['enabled'] == 1) {
                    typeId1 = 'id="' + tableId + elements[5].type + k + '"';
                    tbody += str_replace('%s', typeId1, elements[5].opening) + html + elements[5].closing + "\n";
                }
            }
            tbody += elements[3].closing + "\n";
        }
        tbody += elements[2].closing + "\n";

        result += ((noTHead) ? '' : thead) + tbody;

        result += elements[0].closing + "\n";

        document.querySelector(tableId).innerHTML = result;

        if (typeof callback === 'function') {
            callback.call(this);
        }
    }
    static create() {
        return new Ajil.Web.UI.Table();
    }
}
