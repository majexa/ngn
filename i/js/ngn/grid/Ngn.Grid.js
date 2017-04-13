/**
 * Компонент для вывода и редактирования табличных данных
 */
Ngn.Grid = new Class({
  Extends: Ngn.Items.Table,

  options: {
    basePath: window.location.pathname, // Пример: `http://localhost:8080`
    restBasePath: '', // Пример: `/api/v1`
    basicBasePath: '', // Пример: `/user`. Для запросов получения записей к этому пути прибаыляется `s` на конце

    listAction: null, // используется для замены прямых ссылок на страницы пагинации на listAjaxAction
    listAjaxAction: null, //
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
    valueContainerClass: 'v',
    resizeble: false,
    search: false,
    requestOptions: {}
  },

  btns: {},

  init: function() {
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

  initMenu: function() {
    var grid = this, action;
    this.eMenu = Elements.from('<div class="itemsTableMenu dgray"><div class="clear"></div></div>')[0].inject(this.eParent);
    if (!this.options.menu) return;
    for (var i = 0; i < this.options.menu.length; i++) {
      (function() {
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
    if (!ajax) ajax = true;
    return this.options.basePath + this._getLink(ajax);
  },

  _getLink: function(ajax) {
    if (!ajax) {
      throw new Error('non-ajax part is not realized');
    }
    return this.options.restBasePath + '/' + this.options.basicBasePath;
  },

  _getListLink: function(ajax) {
    if (!ajax) {
      throw new Error('non-ajax part is not realized');
    }
    return this.options.restBasePath + '/' + //
      (this.options.basicBasePath ? this.options.basicBasePath + 's' : '') + //
      ((this.options.filterPath ? this.options.filterPath.toPathString() : ''));
  },

  getListLink: function(ajax) {
    return this.options.basePath + this._getListLink(ajax);
  },

  reload: function(itemId, skipLoader) {
    if (itemId && !skipLoader) this.loading(itemId, true); // показываем, что строчка обновляется
    Ngn.Request.Iface.loading(true);
    new Ngn.Request.JSON(Object.merge({
      url: this.getListLink(true),
      onComplete: function(r) {
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

  initBody: function(rows) {
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
        if (this.options.formatters[index]) value = this.options.formatters[index](value, row.id);
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
      'class': 'total help',
      title: Ngn.Locale.get('core.totalItemsCount'),
      html: data.itemsTotal
    }).inject(this.ePagination);
    this.ePagination.getElements('a').each(function(el) {
      if (!fromAjax) return;
      el.store('href', el.get('href'));
      el.set('href', this.replaceLink(el.get('href'), fromAjax));
      el.addEvent('click', function(e) {
        Ngn.Request.Iface.loading(true);
        this.currentPage = el.get('href').replace(/.*pg(\d+)/, '$1');
        var link = this.getListLink(true);
        if (link.match(/pg(\d+)/)) {
          link = link.replace(/pg(\d+)/, 'pg' + this.currentPage)
        } else {
          link += '/pg' + this.currentPage;
        }
        new Ngn.Request.JSON(Object.merge({
          url: link,
          onComplete: function(r) {
            Ngn.Request.Iface.loading(false);
            this.initInterface(r, true);
          }.bind(this)
        }, this.options.requestOptions)).get();
        return false;
      }.bind(this));
    }.bind(this));
  },

  createToolBtn: function(toolName, row, action) {
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
  },

  // from Items

  getDeleteLink: function(id) {
    return this.getLink(true) + 'json_delete/' + id;
  },

  initToolActions: function () {
    this.addBtnsActions([
      ['.delete', function (id, eBtn, eItem) {
        new Ngn.Dialog.Confirm.Mem(Object.merge({
          id: 'itemsDelete',
          notAskSomeTime: true,
          onOkClose: function () {
            this.loading(id, true);
            new Ngn.Request.JSON({
              url: this.getDeleteLink(id),
              onComplete: function () {
                eItem.destroy();
                //this.options.reloadOnDelete ? this.reload() :
              }.bind(this)
            }).get();
          }.bind(this)
        }, Ngn.Grid.defaultDialogOpts));
      }.bind(this)],
      ['a[class~=flagOn],a[class~=flagOff]', function (id, eBtn) {
        /*
         var eFlagName = eBtn.getElement('i');
         var flagName = eFlagName.get('title');
         eFlagName.removeProperty('title');
         el.addEvent('click', function(e){
         var flag = eBtn.get('class').match(/flagOn/) ? true : false;
         e.preventDefault();
         //eLoading.addClass('loading');
         var post = {};
         post[this.options.idParam] = id;
         post.k = flagName;
         post.v = flag ? 0 : 1;
         new Request({
         url: window.location.pathname + '?a=ajax_updateDirect',
         onComplete: function() {
         eBtn.removeClass(flag ? 'flagOn' : 'flagOff');
         eBtn.addClass(flag ? 'flagOff' : 'flagOn');
         //eLoading.removeClass('loading');
         }
         }).GET(post);
         }.bind(this));
         */
      }.bind(this)]
    ]);
    this.addBtnAction();
  },

  switcherClasses: [],

  _addBtnAction: function (eItem, selector, action) {
    if (!eItem) return;
    var eBtn = eItem.getElement(selector);
    if (!eBtn) return;
    eBtn.addEvent('click', function (e) {
      e.preventDefault();
      action(eItem.retrieve('itemId'), eBtn, eItem);
    }.bind(this));
  },

  addBtnAction: function (selector, action) {
    Object.every(this.esItems, function (eItem) {
      this._addBtnAction(eItem, selector, action);
    }.bind(this));
  },

  addBtnsActions: function (actions) {
    for (var i in this.esItems) {
      var eItem = this.esItems[i];
      for (var j = 0; j < actions.length; j++) {
        this._addBtnAction(eItem, actions[j][0], actions[j][1]);
      }
    }
  },


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
    return Object.merge({
      id: 'dlgNew',
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
  title: Ngn.Locale.get('Core.create'),
  cls: 'add',
  action: Ngn.GridBtnAction.New
};
Ngn.Grid.defaultMenu = [Ngn.Grid.menu['new']];

Ngn.Grid.toolActions = {};
Ngn.Grid.toolActions.edit = function(row, btn) {
  new Ngn.Dialog.RequestForm(Object.merge({
    id: 'dlgEdit' + row.id,
    url: this.options.basePath + this.options.ajaxBasePath + '/json_edit?id=' + row.id,
    width: 500,
    height: 300,
    title: false,
    dialogClass: 'dialog fieldFullWidth',
    onOkClose: function() {
      this.reload(row.id);
    }.bind(this)
  }, Ngn.Grid.defaultDialogOpts));
};