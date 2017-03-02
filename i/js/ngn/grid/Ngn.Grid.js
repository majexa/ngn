/**
 * Компонент для вывода и редактирования табличных данных
 */
Ngn.Grid = new Class({
  Extends: Ngn.Items.Table,

  options: {
    isSorting: false,
    tools: {},
    formatters: {},
    itemElementSelector: 'tbody .item',
    checkboxes: false,
    filterPath: null,
    id: null,
    toolActions: {},
    toolLinks: {},
    eParent: '#table',
    replaceLocation: false,
    listAction: null, // используется как путь к странице со списком записей относительно базовых `basePath` + `basicbBasePath` путей
    listAjaxAction: null, // используется как путь к JSON-запросу на получения записей, относительно базовых `basePath` + `basicbBasePath` путей
    valueContainerClass: 'v',
    basePath: window.location.pathname,
    basicBasePath: '',
    resizeble: false,
    search: false,
    requestOptions: {}
  },

  btns: {},

  init: function () {
    if (!this.options.eParent) throw new Ngn.EmptyError('this.options.eParent');
    if (this.options.basePath == '/') this.options.basePath = '';
    if (this.options.basePath && !this.options.id) this.options.id = Ngn.String.hashCode(this.options.basePath);
    this.eParent = document.getElement(this.options.eParent);
    this.initMenu();
    if (this.options.search) new Ngn.Grid.Search(this);
    this.options.eItems = Elements.from('<table width="100%" cellpadding="0" cellspacing="0" class="items itemsTable' + (this.options.resizeble ? ' resizeble' : '') + '"><thead><tr></tr></thead><tbody></tbody></table>')[0].inject(this.eParent);
    this.eHeadTr = this.options.eItems.getElement('thead tr');
    if (this.options.checkboxes) {
      Elements.from('<th class="tools"><input type="checkbox" id="checkAll" title="Выделить всё" class="tooltip" /></th>')[0].inject(this.eHeadTr);
    } else {
      Elements.from('<th></th>')[0].inject(this.eHeadTr);
    }
    if (this.options.data) this.initInterface(this.options.data);
  },

  initMenu: function () {
    var grid = this, action;
    this.eMenu = Elements.from('<div class="itemsTableMenu dgray"><div class="clear"></div></div>')[0].inject(this.eParent);
    if (!this.options.menu) return;
    for (var i = 0; i < this.options.menu.length; i++) {
      (function () {
        var v = grid.options.menu[i];
        var keys = Object.keys(v.action);
        if (keys.length && Ngn.Arr.inn('$constructor', keys)) {
          // класс Ngn.GridBtnAction.*
          action = new v.action(grid);
          action.id = v.cls;
        } else {
          if (typeof(v.action) == 'function') {
            // ф-я function(grid) {}
            action = {action: v.action};
          } else {
            action = v.action ? {action: v.action} : null;
          }
          if (action) {
            action.id = v.cls;
            action.args = grid;
          }
        }
        var cls = v.cls;
        v.cls = 'btn ' + v.cls;
        grid.btns[cls] = new Ngn.Btn(Ngn.Btn.btn(v).inject(this.eMenu, 'top'), action, v.options || {});
      }.bind(this))();
    }
  },

  dataLoaded: function (data) {
    this.options.data = data;
    this.init();
    this.initInterface(this.options.data);
  },

  initThSizes: function () {
    this.eHeadTr.getElements('th').each(function (el) {
      el.setStyle('width', el.getSize().x + 'px');
    });
  },

  getLink: function (ajax) {
    if (!ajax) ajax = true;
    return this.options.basePath + this._getLink(ajax);
  },

  _getLink: function (ajax) {
    if (!ajax) {
      throw new Error('non ajax part is not realized');
    }
    return this.options.restBasePath + '/' + this.options.basicBasePath;
  },

  getListLink: function (ajax) {
    return this.options.basePath + this._getListLink(ajax);
  },

  _getListLink: function (ajax) {
    if (!ajax) {
      throw new Error('non ajax part is not realized');
    }
    return this.options.restBasePath + '/' + this.options.basicBasePath + 's';
  },

  // OLD LOGGICMUST DIE!!!!
  // var action = ajax ? this.options.listAjaxAction : this.options.listAction;
  // return (forceBase ? '' : this.options.basePath) + //
  //   (ajax ? this.options.ajaxBasePath : this.options.basicBasePath) + //
  //   (action ? '/' + action : '') + //
  //   (this.options.filterPath ? this.options.filterPath.toPathString() : '');


  reload: function (itemId, skipLoader) {
    if (itemId && !skipLoader) this.loading(itemId, true); // показываем, что строчка обновляется
    Ngn.Request.Iface.loading(true);
    console.log(this.getListLink(true));//
    new Ngn.Request.JSON(Object.merge({
      url: this.getListLink(true),
      onComplete: function (r) {
        // todo: bad support. remove temporary
        if (this.options.replaceLocation && window.history.pushState) {
          window.history.pushState(null, '', this._getLink(false));
        }
        this.initInterface(r, true);
        this.fireEvent('reloadComplete', r);
        Ngn.Request.Iface.loading(false);
        if (itemId) this.rowFlash(itemId);
      }.bind(this)
    }, this.options.requestOptions)).get();
    return this;
  },

  rowFlash: function (itemId) {
    if (!this.esItems[itemId]) return;
    var fx = new Fx.Tween(this.esItems[itemId], {
      duration: 200,
      onComplete: function () {
        fx.setOptions({duration: 3000});
        fx.start('background-color', '#FFFFFF');
      }
    });
    fx.start('background-color', '#FFB900');
  },

  initInterface: function (data, fromAjax) {
    if (data.head) this.initHead(data.head);
    if (data.body) this.initBody(data.body);
    if (data.pagination) this.initPagination(data.pagination, fromAjax);
    this.initItems();
    if (this.options.resizeble) {
      if (!this.options.id) throw new Ngn.EmptyError('this.options.id');
      this.resizeble = new Ngn.Grid.Resizeble(this);
      window.addEvent('resize', function () {
        this.resizeble.resizeLastCol();
      }.bind(this));
    }
  },

  initHead: function (head) {
    head = Ngn.Object.fromArray(head);
    this.esTh = [];
    this.eHeadTr.set('html', '');
    new Element('th').inject(this.eHeadTr);
    for (var i in head) {
      var eTh = new Element('th', {html: head[i]}).inject(this.eHeadTr);
      this.esTh[i] = eTh;
    }
    return this;
  },

  initBody: function (rows) {
    if (!rows) throw new Ngn.EmptyError('rows');
    rows = Ngn.Object.fromArray(rows);
    var eBody = this.options.eItems.getElement('tbody');
    eBody.set('html', '');
    if (Object.keys(rows).length === 0) {
      new Element('td', {
        html: Ngn.Locale.get('Core.noItems'),
        colspan: this.esTh.length + 1,
        'class': 'noItems'
      }).inject(new Element('tr').inject(eBody));
      return;
    }
    for (var k in rows) {
      var row = rows[k];
      if (!row.data) throw new Error('Row ' + k + ' has no data');
      var eRow = new Element('tr', {
        'class': 'item' + (row.rowClass ? ' ' + row.rowClass : ''),
        'id': 'item_' + row.id,
        'data-id': row.id
      }).inject(eBody);
      var eTools = new Element('td', {
        'class': 'tools',
        'html': '<table cellpadding="0" cellspacing="0"><tr></tr></table>'
      }).inject(eRow);
      eTools = eTools.getElement('tr');
      row.el = eRow;
      row.eTools = eTools;
      if (this.options.checkboxes) {
        Elements.from('<td><input type="checkbox" name="itemIds[]" value="' + row.id + '"/></td>')[0].inject(eTools);
      } else {
        // Elements.from('<td></td>')[0].inject(eTools);
      }
      if (this.options.isSorting) Elements.from('<td><div class="dragBox"></div></td>')[0].inject(eTools);
      var n = 0;
      for (var index in Ngn.Object.fromArray(row.data)) {
        var scalar = !(typeOf(row.data[index]) == 'object' || typeOf(row.data[index]) == 'array');
        var prop = {};
        if (!scalar) {
          var value = row.data[index][0];
          prop['class'] = row.data[index][1];
        } else {
          value = row.data[index];
        }
        if (this.options.formatters[index]) value = this.options.formatters[index](value);
        //prop.html = this.replaceHtmlValue(value);
        prop.html = value;
        new Element('td', prop).addClass('n_' + index).addClass(this.options.valueContainerClass).set('data-n', n).inject(eRow);
        n++;
      }
      var tools = Object.merge(row.tools || {}, this.options.tools);
      row.tools = Ngn.Object.fromArray(tools);
      for (var toolName in row.tools) {
        this.createToolBtn(toolName, row);
      }
    }
    return this;
  },

  currentPage: 1,

  replaceLink: function (link, ajax) {
    if (ajax) {
      return link.replace(new RegExp('/' + this.options.listAjaxAction + '/', 'g'), this.options.listAction ? '/' + this.options.listAction + '/' : '/');
    } else {
      return link.replace(new RegExp('/' + this.options.listAction + '/', 'g'), '/' + this.options.listAjaxAction + '/');
    }
  },

  initPagination: function (data, fromAjax) {
    if (this.ePagination) this.ePagination.dispose();
    this.ePagination = Elements.from('<div class="pNums"><div class="bookmarks">' + data.pNums + '</div></div>')[0].inject(this.eMenu, 'top');
    new Element('div', {
      'class': 'total help',
      title: Ngn.Locale.get('core.totalItemsCount'),
      html: data.itemsTotal
    }).inject(this.ePagination);
    this.ePagination.getElements('a').each(function (el) {
      if (!fromAjax) return;
      el.store('href', el.get('href'));
      el.set('href', this.replaceLink(el.get('href'), fromAjax));
      el.addEvent('click', function (e) {
        Ngn.Request.Iface.loading(true);
        this.currentPage = el.get('href').replace(/.*pg(\d+)/, '$1');
        console.log([this.currentPage, this.getLink(true)]);
        var link = this.getListLink(true);
        if (link.match(/pg(\d+)/)) {
          link = link.replace(/pg(\d+)/, 'pg' + this.currentPage)
        } else {
          link += '/pg' + this.currentPage;
        }


        new Ngn.Request.JSON(Object.merge({
          url: link,
          onComplete: function (r) {
            Ngn.Request.Iface.loading(false);
            this.initInterface(r, true);
          }.bind(this)
        }, this.options.requestOptions)).get();
        return false;
      }.bind(this));
    }.bind(this));
  },

  replaceHtmlValue: function (v) {
    if (typeof(v) != 'string') return v;
    return v.replace(new RegExp(this.options.listAjaxAction, 'g'), this.options.listAction);
  },

  createToolBtn: function (toolName, row, action) {
    var tool = row.tools[toolName];


    if (tool.type) {
      Ngn.Items.toolActions[tool.type].init(this, toolName, row);
      return;
    }

    var cls = ((typeOf(tool) == 'object' && tool.cls) ? tool.cls : toolName);
    // fa fix
    var faCls = cls;
    if (faCls == 'delete') {
      faCls = 'trash';
      var colorCls = 'danger';
    } else {
      colorCls = 'primary';
    }
    // ---
    var el = new Element('button', {
      'href': this.options.toolLinks[toolName] ? this.options.toolLinks[toolName](row) : '#',
      'class': 'btn btn-' + colorCls + ' btn-sm  ' + cls,
      'html': '<i class="fa fa-' + faCls + '"></i>',
      'title': typeOf(tool) == 'object' ? tool.title : tool
    }).inject(new Element('td').inject(row.eTools));

    if (typeOf(tool) == 'object') {
      if (tool.target) el.set('target', tool.target);
    }

    action = action || this.options.toolActions[toolName] || false;

    if (action) {
      // Только если экшн определён, биндим на элемент клик (new Ngn.Btn)
      action = action.bind(this);
      new Ngn.Btn(el, function () {
        action(row, this);
      });
    }
    return el;
  },

  idP: function (id) {
    var p = {};
    p[this.options.idParam] = id;
    return p;
  }

});

Ngn.Grid.defaultDialogOpts = {
  width: 500,
  height: 300,
  reduceHeight: true
};

Ngn.Grid.menu = {};

Ngn.GridBtnAction = new Class({
  Extends: Ngn.Btn.Action,
  initialize: function (grid) {
    this.grid = grid;
    this.classAction = true;
  }
});

Ngn.GridBtnAction.New = new Class({
  Extends: Ngn.GridBtnAction,
  action: function () {
    new Ngn.Dialog.RequestForm(this.getDialogOptions());
  },
  getDialogOptions: function () {
    return Object.merge({
      id: 'dlgNew',
      dialogClass: 'dialog fieldFullWidth',
      url: this.grid.options.basePath + '/json_new',
      title: false,
      onOkClose: function () {
        this.grid.reload();
      }.bind(this)
    }, Ngn.Grid.defaultDialogOpts)
  }
});

Ngn.Grid.menu['new'] = {
  title: Ngn.Locale.get('Core.create'),
  cls: 'add',
  action: Ngn.GridBtnAction.New
};
Ngn.Grid.defaultMenu = [Ngn.Grid.menu['new']];

Ngn.Grid.toolActions = {};
Ngn.Grid.toolActions.edit = function (row, btn) {
  new Ngn.Dialog.RequestForm(Object.merge({
    id: 'dlgEdit' + row.id,
    url: this.options.basePath + this.options.ajaxBasePath + '/json_edit?id=' + row.id,
    width: 500,
    height: 300,
    title: false,
    dialogClass: 'dialog fieldFullWidth',
    onOkClose: function () {
      this.reload(row.id);
    }.bind(this)
  }, Ngn.Grid.defaultDialogOpts));
};