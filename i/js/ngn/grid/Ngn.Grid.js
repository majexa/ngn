Ngn.Grid = new Class({
  Extends: Ngn.Items.Table,

  options: {
    //data: {
    //  head: [ 'title1' ],
    //  body: [ { tools: {}, data: [] } ]
    //},
    isSorting: false,
    tools: {},
    formatters: {},
    itemElementSelector: 'tbody .item',
    checkboxes: false,
    filterPath: null,
    id: null,
    toolActions: {},
    toolLinks: {},
    eParent: 'table',
    fromDialog: false,
    listAction: null,
    listAjaxAction: 'json_getItems',
    valueContainerClass: 'v',
    basePath: window.location.pathname,
    resizeble: false
  },

  btns: {},

  init: function() {
    if (!this.options.eParent) throw new Ngn.EmptyError('this.options.eParent');
    if (this.options.basePath == '/') this.options.basePath = '';
    this.eParent = $(this.options.eParent);
    this.initMenu();
    this.options.eItems = Elements.from('<table width="100%" cellpadding="0" cellspacing="0" class="items itemsTable' + (this.options.resizeble ? ' resizeble' : '') + '"><thead><tr></tr></thead><tbody></tbody></table>')[0].inject(this.eParent);
    this.eHeadTr = this.options.eItems.getElement('thead tr');
    if (this.options.checkboxes) {
      Elements.from('<th class="tools"><input type="checkbox" id="checkAll" title="Выделить всё" class="tooltip" /></th>')[0].inject(this.eHeadTr);
    } else {
      Elements.from('<th></th>')[0].inject(this.eHeadTr);
    }
    if (this.options.data) this.initInterface(this.options.data);
  },

  initMenu: function() {
    var grid = this, action;
    this.eMenu = Elements.from('<div class="itemsTableMenu dgray iconsSet"></div>')[0].inject(this.eParent);
    if (!this.options.menu) return;
    for (var i = 0; i < this.options.menu.length; i++) {
      (function() {
        var v = grid.options.menu[i];
        var keys = Object.keys(v.action);
        if (keys.length && in_array('$constructor', keys)) {
          // класс Ngn.GridBtnAction.*
          action = new v.action(grid);
          //action.action.bind(action);
          action.id = v.cls;
          // action.action();
        } else {
          if (typeof(v.action) == 'function') {
            // ф-я function(grid) {}
            action = { action: v.action };
          } else {
            action = v.action ? { action: v.action } : null;
          }
          if (action) {
            action.id = v.cls;
            action.args = grid;
          }
        }
        var cls = v.cls;
        v.cls = 'btn ' + v.cls;
        grid.btns[cls] = new Ngn.Btn(Ngn.btn(v).inject(this.eMenu), action, v.options || {});
      }.bind(this))();
    }
  },

  dataLoaded: function(data) {
    this.options.data = data;
    this.init();
    this.initInterface(this.options.data);
  },

  initThSizes: function() {
    this.eHeadTr.getElements('th').each(function(el) {
      el.setStyle('width', el.getSize().x + 'px');
    });
  },

  getLink: function(ajax) {
    var action = ajax ? this.options.listAjaxAction : this.options.listAction;
    if (!action) if (ajax) throw new Ngn.EmptyError('action');
    return this.options.basePath + (action ? '/' + action : '') + (this.options.filterPath ? this.options.filterPath.toPathString() : '') + (this.currentPage == 1 ? '' : '/pg' + this.currentPage);
  },

  reload: function(itemId, skipLoader) {
    if (itemId && !skipLoader) this.loading(itemId, true); // показываем, что строчка обновляется
    Ngn.loading(true);
    new Ngn.Request.JSON({
      url: this.getLink(true),
      onComplete: function(r) {
        if (!this.options.fromDialog) {
          if (window.history.pushState) window.history.pushState(null, null, this.getLink(false));
        }
        this.initInterface(r, true);
        this.fireEvent('reloadComplete', r);
        Ngn.loading(false);
        this.rowFlash(itemId);
      }.bind(this)
    }).send();
    return this;
  },

  rowFlash: function(itemId) {
    if (!this.esItems[itemId]) return;
    var fx = new Fx.Tween(this.esItems[itemId], {
      duration: 200,
      onComplete: function() {
        fx.setOptions({duration: 3000});
        fx.start('background-color', '#FFFFFF');
      }
    });
    fx.start('background-color', '#FFB900');
  },

  initInterface: function(data, fromAjax) {
    if (data.head) this.initHead(data.head);
    if (data.body) this.initBody(data.body);
    if (data.pagination) this.initPagination(data.pagination, fromAjax);
    this.initItems();
    if (this.options.resizeble) {
      if (!this.options.id) throw new Ngn.EmptyError('this.options.id');
      this.resizeble = new Ngn.Grid.Resizeble(this);
      window.addEvent('resize', function() {
        this.resizeble.resizeLastCol();
      }.bind(this));
    }
  },

  initHead: function(head) {
    head = Ngn.arrToObj(head);
    this.esTh = [];
    this.eHeadTr.set('html', '');
    new Element('th').inject(this.eHeadTr);
    for (var i in head) {
      var eTh = new Element('th', {html: head[i]}).inject(this.eHeadTr);
      this.esTh[i] = eTh;
    }
    return this;
  },

  initBody: function(rows) {
    if (!rows) throw new Ngn.EmptyError('rows');
    rows = Ngn.arrToObj(rows);
    var eBody = this.options.eItems.getElement('tbody');
    eBody.set('html', '');
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
        Elements.from('<td></td>')[0].inject(eTools);
      }
      if (this.options.isSorting) Elements.from('<td><div class="dragBox"></div></td>')[0].inject(eTools);
      var n = 0;
      for (var name in Ngn.arrToObj(row.data)) {
        var prop = {};
        if (typeOf(row.data[name]) == 'object') {
          var value = row.data[name][0];
          prop['class'] = row.data[name][1];
        } else {
          value = row.data[name];
        }
        if (this.options.formatters[name]) value = this.options.formatters[name]();
        prop.html = this.replaceHtmlValue(value);
        new Element('td', prop).addClass(this.options.valueContainerClass).set('data-n', n).inject(eRow);
        n++;
      }
      row.tools = $merge(row.tools || {}, this.options.tools);
      for (var cls in Ngn.arrToObj(row.tools)) {
        if (typeOf(row.tools[cls]) == 'object') {
          if (!row.tools[cls].type) throw new Error('row.tools[cls].type must be defined');
          if (Ngn.Items.toolActions[row.tools[cls].type]) {
            Ngn.Items.toolActions[row.tools[cls].type].init(this, cls, row);
          } else {
            throw new Error(row.tools[cls].type + ' toolAction not defined');
          }
        } else {
          if (Ngn.Items.toolActions[cls]) {
            Ngn.Items.toolActions[cls].init(this, cls, row);
          } else {
            this.createToolBtn(cls, row);
          }
        }
      }
    }
    return this;
  },

  currentPage: 1,

  replaceLinkAjaxToNormal: function(link) {
    return;
  },

  replaceLink: function(link, ajax) {
    if (ajax) {
      return link.replace(new RegExp('/' + this.options.listAjaxAction + '/', 'g'), this.options.listAction ? '/' + this.options.listAction + '/' : '/');
    } else {
      return link.replace(new RegExp('/' + this.options.listAction + '/', 'g'), '/' + this.options.listAjaxAction + '/');
    }
  },

  initPagination: function(data, fromAjax) {
    if (this.ePagination) this.ePagination.dispose();
    this.ePagination = Elements.from('<div class="pNums"><div class="bookmarks">' + data.pNums + '</div></div>')[0].inject(this.eMenu, 'top');
    new Element('div', {
      'class': 'total',
      html: 'Всего записей: ' + data.itemsTotal
    }).inject(this.ePagination);
    this.ePagination.getElements('a').each(function(el) {
      if (fromAjax) el.store('href', el.get('href'));
      el.set('href', this.replaceLink(el.get('href'), fromAjax));
      if (!fromAjax) el.store('href', el.get('href'));
      el.addEvent('click', function(e) {
        new Event(e).stop();
        Ngn.loading(true);
        this.currentPage = el.get('href').replace(/.*pg(\d+)/, '$1');
        new Ngn.Request.JSON({
          url: el.retrieve('href'),
          onComplete: function(r) {
            Ngn.loading(false);
            this.initInterface(r, true);
          }.bind(this)
        }).send();
      }.bind(this));
    }.bind(this));
  },

  replaceHtmlValue: function(v) {
    if (typeof(v) != 'string') return v;
    return v.replace(new RegExp(this.options.listAjaxAction, 'g'), this.options.listAction);
  },

  createToolBtn: function(cls, row, action) {
    var action = action || this.options.toolActions[cls] || false;
    var el = new Element('a', {
      'href': this.options.toolLinks[cls] ? this.options.toolLinks[cls](row) : '#',
      'class': 'iconBtn ' + cls,
      'html': '<i></i>',
      'title': row.tools[cls]
    }).inject(new Element('td').inject(row.eTools));
    if (action) {
      // Только если экшн определён, биндим на элемент клик (new Ngn.Btn)
      action = action.bind(this);
      new Ngn.Btn(el, function() {
        action(row, this);
      });
    }
    return el;
  },

  idP: function(id) {
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
  initialize: function(grid) {
    this.grid = grid;
    this.classAction = true;
  }
});

Ngn.GridBtnAction.New = new Class({
  Extends: Ngn.GridBtnAction,
  action: function() {
    new Ngn.Dialog.RequestForm(this.getDialogOptions());
  },
  getDialogOptions: function() {
    return $merge({
      id: 'CHANGE_ME',
      dialogClass: 'dialog fieldFullWidth',
      url: this.grid.options.basePath + '/json_new',
      title: false,
      onOkClose: function() {
        this.grid.reload();
      }.bind(this)
    }, Ngn.Grid.defaultDialogOpts)
  }
});

Ngn.Grid.menu['new'] = {
  title: 'Создать',
  cls: 'add',
  action: Ngn.GridBtnAction.New
};
Ngn.Grid.defaultMenu = [Ngn.Grid.menu['new']];

Ngn.Grid.toolActions = {};
Ngn.Grid.toolActions.edit = function(row, opt) {
  new Ngn.Dialog.RequestForm($merge({
    id: 'CHANGE_ME',
    url: this.options.basePath + '?a=json_edit&id=' + row.id,
    width: 500,
    height: 300,
    title: false,
    onOkClose: function() {
      this.reload(row.id);
    }.bind(this)
  }, Ngn.Grid.defaultDialogOpts));
};