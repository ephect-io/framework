var Ajil = Ajil || {}

Ajil.Url = class _Url {
    constructor(url, domain, isSSL) {
        this._url = url;
        this._isParsed = false;
        this._isSSL = (isSSL !== undefined) ? isSSL : (window.location.protocol == 'https:');
        
        this._tmpDomain = domain;
        this._port = window.location.port;
        this._page = window.location.pathname;
        this._domain = this._url;
        this._isRelative = false;
        this.parse();
    }
    parse() {
        var result = [];
        this._protocol = '';
        if (this._tmpDomain !== undefined) {
            this._protocol = (this._tmpDomain.search('://') > -1) ? this._tmpDomain.substring(0, this._tmpDomain.search('://') + 1) : '';
        }
        else {
            this._protocol = (this._url.search('://') > -1) ? this._url.substring(0, this._url.search('://') + 1) : '';
        }
        if (this._protocol === '' && this._tmpDomain === undefined) {
            this._page = this._url;
            this._isRelative = true;
            this._protocol = window.location.protocol;
            this._domain = window.location.hostname;
            this._port = window.location.port;
            //this._url = window.location.href.substring(0, window.location.href.search('/'));
        }
        else {
            if (this._protocol === '' && this._tmpDomain !== undefined) {
                this._domain = this._tmpDomain;
                this._protocol = (this._isSSL) ? 'https:' : 'http:';
            }
            else {
                if (this._protocol === '') {
                    this._protocol = (this._isSSL) ? 'https:' : 'http:';
                    //throw new Error('Invalid absolute url. Protocol is missing');
                }
                this._url = this._url.replace(this._protocol + '//', '');
                var domainLimit = this._url.search('/');
                if (domainLimit > 0) {
                    this._domain = this._url.substring(0, domainLimit);
                    this._url = this._url.replace(this._domain, '');
                }
                else if (this._tmpDomain !== undefined) {
                    this._domain = this._tmpDomain;
                }
                else {
                    this._domain = this._url;
                    this._url = '/';
                }
                if (this._domain.search(':') > -1) {
                    this._port = this._domain.substring(this._domain.search(':'));
                    this._url = this._url.replace(':' + this._port, '');
                }
                if (this._domain.search('localhost') > -1) {
                    this._domain = 'localhost';
                    this._url = this._url.replace(this._domain, '');
                }
            }
            this._page = this._url;
            if (this._page.substring(0, 1) === '/') {
                this._page = this._page.substring(1);
            }
            this._port = (this._port === '') ? (this._isSSL) ? '443' : '80' : this._port;
            this._protocol = ((this._domain !== '' && this._protocol === '') ? ((this._isSSL) ? 'https:' : 'http:') : this._protocol);
        }
        var queryString = '';
        if (this._page.search(/\?/) > -1) {
            queryString = this._page.substring(this._page.search(/\?/));
        }
        this._queryString = queryString;
        result.isRelative = this._isRelative;
        result.protocol = this._protocol;
        result.domain = this._domain;
        result.port = this._port;
        result.page = this._page;
        result.queryString = this._queryString;
        this._url = result;
        this._isParsed = true;
        return result;
    }
    toString() {
        if (!this._isParsed) {
            this.parse();
        }
        var fqUrl = (this._queryString !== '') ? this._page + this._queryString : this._page;
        fqUrl = this._protocol + '//' + (this._domain + (this._port !== '80' && this._port !== '443' ? ':' + this._port : '') + Ajil.DOM.rewriteBase + fqUrl).replace(/\/\//g, '/');
        return fqUrl;
    }
}
