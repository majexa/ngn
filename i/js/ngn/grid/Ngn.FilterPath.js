/**
 * Компонент создания фильтров для грида
 */
Ngn.FilterPath = new Class({



  /**
   * "/path/to/page/param1/param2" - здесь n=3
   */
  initialize: function(n) {
    this.n = n;
    this.setPg(window.location.pathname);
    this.chunks = [];
    this.filters = {};
    var p = window.location.pathname.split('/');
    var chunks = p.slice(this.n + 1, p.length);
    for (var i = 0; i < chunks.length; i++) {
      if (!chunks[i].contains('.')) continue;
      this.chunks.push(chunks[i]);
    }
    for (i = 0; i < this.chunks.length; i++) {
      p = this.chunks[i].split('.');
      if (p.length == 3) {
        this._addFilter(p[0], p[1], p[2]);
      } else if (p.length == 2) {
        this._addFilter(p[0], 'default', p[1]);
      } else {
        // ignore
      }
    }
  },

  reset: function() {
    this.filters = {};
  },

  _addFilter: function(type, name, value, multiple) {
    if (!this.filters[type]) this.filters[type] = {};
    this.filters[type][name] = value;
  },

  toPathString: function() {
    var s = '';
    s += '/pg' + this.pg;
    for (var type in this.filters) {
      for (var name in this.filters[type]) {
        s += '/' + type + '.' + (name == 'default' ? '' : name + '.') + //
          this.filters[type][name];
      }
    }
    return s;
  },

  addFilter: function(type, name, value) {
    if (!type) throw new Error('type not defined');
    if (value === '' || value === false) {
      delete this.filters[type][name];
    } else {
      this._addFilter(type, name, value);
    }
  },

  removeFilter: function(type, name) {
    if (!this.filters[type][name]) return;
    this.addFilter(type, name, '');
  },

  setPg: function(path) {
    this.pg = path.test(/\/pg\d+/) ? parseInt(path.replace(/.*\/pg(\d+).*/, '$1')) : 1;
  }

});