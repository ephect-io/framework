var Ajil = Ajil || {}
Ajil.Web = Ajil.Web || {}
Ajil.Web.UI = Ajil.Web.UI || {}

Ajil.Web.UI.Plugin = class _Plugin extends Ajil.Web.Object {
    constructor() {
        super();
    }
    dataBind(tableId, values, templates) {
        let colNum = templates.length;
        let rowNum = values.length;
        for (let j = 0; j < rowNum; j++) {
            let row = values[j];
            for (let i = 0; i < colNum; i++) {
                let template = templates[i];
                let html = row[i];
                if (template.content !== null && template.enabled) {
                    html = template.content;
                    let event = template.event;
                    let e = event.split('#');
                    if (e[0] === 'href') {
                        event = 'javascript:' + e[1];
                    }
                    else {
                        event = e[0] + '="' + e[1] + '"';
                    }
                    for (let m = 0; m < colNum; m++) {
                        html = html.replace('{{ ' + templates[m].name + ' }}', row[m]);
                        event = event.replace(templates[m].name, row[m]);
                        html = html.replace('{{ &' + templates[m].name + ' }}', event);
                    }
                }
                if (template.enabled) {
                    document.querySelector(tableId + 'td' + (i + colNum * j).toString()).innerHTML = html;
                }
            }
        }
    }
    static create() {
        return new Ajil.Web.UI.Plugin();
    }
    static applyTemplate(templates, row, i) {
        let html = row[i];
        //    if(templates[i] === undefined) {
        //        return html;
        //    }
        if (templates[i].content !== '' && templates[i].enabled) {
            html = templates[i].content;
            let event = templates[i].event;
            let e = event.split('#');
            if (e[0] === 'href') {
                event = 'javascript:' + e[1];
            }
            else {
                event = e[0] + '="' + e[1] + '"';
            }
            for (let m = 0; m < templates.length; m++) {
                //            if(templates[m] === undefined) continue;
                html = html.replace('{{ ' + templates[m].name + ' }}', row[m]);
                html = html.replace('{{ ' + templates[m].name + ':index }}', m);
                event = event.replace(templates[m].name, "'" + row[m] + "'");
                html = html.replace('{{ &' + templates[m].name + ' }}', event);
            }
        }
        return html;
    }
    static applyDragHelper(templates, row, i) {
        let html = row[i];
        if (templates[i].dragHelper !== '' && templates[i].enabled) {
            html = templates[i].dragHelper;
            let event = templates[i].event;
            let e = event.split('#');
            if (e[0] === 'href') {
                event = 'javascript:' + e[1];
            }
            else {
                event = e[0] + '="' + e[1] + '"';
            }
            for (let m = 0; m < row.length; m++) {
                html = html.replace('{{ ' + templates[m].name + ' }}', row[m]);
                html = html.replace('{{ ' + templates[m].name + ':index }}', m);
                event = event.replace(templates[m].name, "'" + row[m] + "'");
                html = html.replace('{{ &' + templates[m].name + ' }}', event);
            }
        }
        return html;
    }
}
