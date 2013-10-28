Ngn.DdFilterPath = new Class({

  // "/path/to/page/param1/param2" - здесь n=3
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

  toPathString: function(path) {
    var s = '';
    for (var type in this.filters) {
      for (var name in this.filters[type]) {
        s += '/' + type + '.' + (name == 'default' ? '' : name + '.') + this.filters[type][name];
      }
    }
    if (this.pg > 1) s += '/pg' + this.pg;
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

Ngn.DdFilterPath.date = {};
Ngn.DdFilterPath.date.toObj = function(str) {
  var m = str.match(/(\d+);(\d+);(\d+)-(\d+);(\d+);(\d+)/);
  if (!m) return false;
  return {
    from: [m[1], m[2], m[3]],
    to: [m[4], m[5], m[6]]
  };
};
Ngn.DdFilterPath.date.toStr = function(obj) {
  return obj.from.join(';') + '-' + obj.to.join(';');
};

Ngn.DdFilterPath.Interface = new Class({
  Implements: [Options],

  options: {
    groupToggler: true
  },

  /**
   * @param Ngn.Grid
   * @param Ngn.DdForm
   */
  initialize: function(grid, filtersForm, options) {
    this.setOptions(options);
    this.translate = {};
    this.grid = grid;
    if (!this.grid.options.filterPath) throw new Error('Grid must be initialized with filterPath option');
    this.filterPath = this.grid.options.filterPath;
    this.filtersForm = filtersForm;

    new Element('input', {type: 'reset', value: ' Сбросить фильтры ', 'class': 'resetAll'}).inject(this.filtersForm.eForm, 'top').addEvent('click', function(e) {
      this.filterPath.reset();
      this.grid.reload();
      this.resetMarkers();
    }.bind(this));

    if (this.options.groupToggler) this.groupToggler = new Ngn.GroupToggler(this.filtersForm.eForm);
    this.initFromPath();
    this.initEvents(this.filtersForm.eForm);
    this.filtersForm.addEvent('newElement', this.initEvents.bind(this));
  },

  initFromPath: function() {
    var pathType, name, value;
    for (pathType in this.filterPath.filters) {
      for (name in this.filterPath.filters[pathType]) {
        value = this.filterPath.filters[pathType][name];
        if (pathType == 'd') {
          this.filtersForm.els.dateCreate.setValue(Ngn.DdFilterPath.date.toObj(value));
          this.addMarker('dateCreate');
        } else {
          value = value.split(',');
          var els = Ngn.frm.getElements(name);
          for (var i = 0; i < els.length; i++) {
            if (els[i].get('type') == 'checkbox') {
              if (Arr.inn(els[i].get('value'), value)) els[i].set('checked', true);
            } else {
              els[i].set('value', value);
            }
          }
          this.addMarker(name);
        }
      }
    }
  },

  initEvents: function(eParent) {
    eParent.getElements('select.allowReload,input[type=checkbox]').each(function(el) {
      el.addEvent('change', function() {
        var name, value;
        if (el.get('type') == 'checkbox' && el.getParent('.tagsTreeSelect')) {
          var pathFilterTypeDataEl = el.getParent('.tagsTreeSelect');
          var values = Ngn.frm.toObj(pathFilterTypeDataEl);
          name = pathFilterTypeDataEl.get('data-name');
          value = values[name] ? values[name].join(',') : false;
        } else {
          pathFilterTypeDataEl = el;
          name = el.get('data-name') || el.get('name');
          value = el.get('value');
        }
        value ? this.addMarker(name) : this.removeMarker(name);
        this.filterPath.addFilter(pathFilterTypeDataEl.get('data-pathFilterType'), name, value);
        this.grid.reload();
      }.bind(this));
    }.bind(this));
    eParent.getElements('.type_dateRange input').each(function(eInput) {
      eInput.addEvent('change', function() {
        this.filterPath.addFilter('d', 'default', eInput.get('value'));
        this.addMarker('dateCreate');
        this.grid.reload();
      }.bind(this));
      /*
       return;
       el.getElement('.ok').addEvent('click', function() {
       var d = Ngn.frm.toObj(el);
       c(d);

       return;
       if (Object.eq(d.from, d.to)) {
       if (Object.isEmpty(d.from)) {
       this.filterPath.removeFilter('d', 'default');
       } else {
       this.filterPath.addFilter('d', 'default', d.from.join(';'));
       }
       } else {
       this.filterPath.addFilter('d', 'default', d.from.join(';') + '-' + d.to.join(';'));
       }
       this.addMarker('dateCreate');
       this.grid.reload();
       }.bind(this));
       el.getElement('.reset').addEvent('click', function() {
       Ngn.frm.getElements('from').each(function(el) {
       el.set('value', '');
       });
       Ngn.frm.getElements('to').each(function(el) {
       el.set('value', '');
       });
       this.filterPath.removeFilter('d', 'default');
       this.removeMarker('dateCreate');
       this.grid.reload();
       }.bind(this));
       el.getElement('.today').addEvent('click', function() {
       var d = new Date();
       var today = [d.getDate(), d.getMonth() + 1, d.getFullYear()];
       Ngn.frm.getElements('from').each(function(el, n) {
       el.set('value', today[n]);
       });
       Ngn.frm.getElements('to').each(function(el, n) {
       el.set('value', today[n]);
       });
       });
       */
    }.bind(this));
    eParent.getElements('.type_num input').each(function(el) {
      new Element('input', { type: 'button', value: 'ok' }).inject(el, 'after').addEvent('click', function() {
        this.filterPath.addFilter('v', el.get('name'), el.get('value'));
        this.grid.reload();
      }.bind(this));
    }.bind(this));
  },

  initPagination: function() {
    if (!this.grid.ePagination) return;
    this.grid.ePagination.getElements('a').each(function(el) {
      el.addEvent('click', function(e) {
        e.preventDefault();
        this.filterPath.setPg(el.get('href'));
        this.grid.reload();
      }.bind(this));
    }.bind(this));
    this.grid.addEvent('reloadComplete', function(r) {
      this.grid.ePagination.set('html', '');
      if (r.pNums) {
        this.grid.ePagination.set('html', r.pNums);
        this.initPagination();
      }
    }.bind(this));
  },

  resetMarkers: function() {
    this.filtersForm.eForm.getElements('.element .label').each(function(el) {
      el.removeClass('sel');
    });
  },

  addMarker: function(name) {
    if (!name) throw new Error('name not defined');
    this.filtersForm.eForm.getElement('.element.name_' + name).getElement('.label').addClass('sel');
  },

  removeMarker: function(name) {
    this.filtersForm.eForm.getElement('.element.name_' + name).getElement('.label').removeClass('sel');
  }

});

Ngn.DdFilterPath.getUrl = function() {
  return window.location.pathname.replace(/(.*)\/pg\d+/, '$1');
};