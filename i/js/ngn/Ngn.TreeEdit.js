  Ngn.TreeEdit = new Class({

  Implements: [Events, Options],

  options: {
    id: 'te',
    enableStorage: true,
    onTreeLoad: null,
    folderOpenIcon: 'ngn-tree-folder-open-icon',  // default css class open icon
    folderCloseIcon: 'ngn-tree-folder-close-icon', // default css class close icon
    pageIcon: 'ngn-tree-page-icon',  // default css class open icon
    mifTreeOptions: {},
    activeIfSelected: ['rename', 'delete']
    // buttons: element container with buttons
  },

  initialize: function(container, options) {
    this.container = $(container) || container;
    this.containerInitHtml = this.container.get('html');
    this.setOptions(options);
    this.eButtons = this.options.buttons ? $(this.options.buttons) || this.options.buttons : false;
    this.initUrl();
    this.addEvent('createComplete', this.fireEvent.pass('update', this)).addEvent('moveComplete', this.fireEvent.pass('update', this)).addEvent('renameComplete', this.fireEvent.pass('update', this)).addEvent('deleteComplete', this.fireEvent.pass('update', this));
    this.addEvent('dataLoad', function() {
      (function() {
        var s = this.container.getSize();
        this.eTreeShade = new Element('div', {
          'class': 'treeShade',
          styles: {
            'z-index': 1000,
            'width': s.x + 'px',
            'height': s.y + 'px',
            'position': 'absolute',
            'top': '0px',
            'left': '0px',
            'visibility': 'hidden'
          }
        }).inject(this.container, 'bottom');
        Ngn.bindSizes(this.container, this.eTreeShade);
      }.bind(this)).delay(10);
    }.bind(this));
  },

  loading: function(flag) {
    if (!this.eTreeShade) {
      flag ? this.container.addClass('loader') : this.container.removeClass('loader');
      return;
    }
    if (flag) {
      this.switchButtons(false);
      this.eTreeShade.setStyle('visibility', 'visible');
      this.eTreeShade.addClass('loader');
    } else {
      this.switchButtons(true);
      this.eTreeShade.setStyle('visibility', 'hidden');
      this.eTreeShade.removeClass('loader');
    }
  },

  getTreeOptions: function() {
    return {
      id: this.options.id || 'engine',
      container: this.container, // tree container
      forest: true,
      initialize: function(disableDragging) {
        //new Ngn.Tree.KeyNav(this);
        new Ngn.Tree.Drag(this);
      },
      types: {// node types
        folder: {
          openIcon: this.options.folderOpenIcon,
          closeIcon: this.options.folderCloseIcon
        },
        page: {
          closeIcon: this.options.pageIcon
        }
      },
      dfltType: 'folder', // default node type
      height: 18 // node height
    };
  },

  init: function() {
    // this.defaults.openIcon = this.options.folderOpenIcon;
    // this.defaults.closeIcon = this.options.folderCloseIcon;
    this.tree = new Ngn.Tree($merge(this.getTreeOptions(), this.options.mifTreeOptions));
    this.tree.wrapper.addEvent('click', function(e) {
      if (e.target == this.tree.wrapper) this.tree.unselect();
    }.bind(this));
    this.tree.addEvent('move', function(from, to, where) {
      this.loading(true);
      new Ngn.Request({
        url: this.url + '?a=ajax_move',
        onSuccess: function() {
          this.loading(false);
          this.removeStoredTreeData();
          this.fireEvent('moveComplete');
        }.bind(this)
      }).GET({
          id: from.data.id,
          toId: to.data.id,
          where: where
        });
    }.bind(this));
    this.tree.addEvent('rename', function(node, newName, oldName) {
      if (newName == oldName) return;
      this.loading(true);
      new Ngn.Request({
        url: this.url + '?a=ajax_rename',
        onSuccess: function() {
          this.loading(false);
          this.removeStoredTreeData();
          this.fireEvent('renameComplete', node);
        }.bind(this)
      }).GET({
          id: node.data.id,
          title: newName
        });
    }.bind(this));
    this.tree.addEvent('remove', function(node, newName, oldName) {
      if (!node.data) return;
      this.loading(true);
      new Ngn.Request({
        url: this.url + '?a=ajax_delete',
        onComplete: function(r) {
          this.loading(false);
          this.removeStoredTreeData();
          this.fireEvent('deleteComplete', node);
        }.bind(this)
      }).GET({
          id: node.data.id
        });
    }.bind(this));
    this.tree.addEvent('add', function(node, current, where) {
      this.select(node);
    });
    this.bindButtons();
    this.toggleButtons();
    //this.initCollapseButtons();
    if (this.options.enableStorage) this.stateStorage = new Ngn.TreeStateStorage(this.tree);
    this.initLoadEvents();
    this.loadFromStorage() || this.load();
    return this;
  },

  getTreeDragOptions: function() {
    return {};
  },

  remove: function() {
    if (!confirm('Вы уверены?')) return false;
    this.tree.selected.remove();
  },

  add: function() {
    var title = prompt('Введите название');
    if (!title) return;
    this.create(title);
  },

  create: function(title, action, data) {
    if (!$defined(action)) action = 'create';
    if (!$defined(data)) data = {};
    if (this.tree.selected && this.tree.selected.data) {
      // Узел выбран
      var where = 'inside';
      var current = this.tree.selected;
    } else if (this.tree.root) {
      // Узел не выбран. Добавляем в начало корня
      var where = 'before';
      var current = this.tree.root.getFirst();
    }
    this.loading(true);
    new Ngn.Request.JSON({
      url: this.url + '?a=json_' + action,
      onComplete: function(r) {
        this.loading(false);
        this.removeStoredTreeData();
        this.reload();
        this.fireEvent('createComplete');
      }.bind(this)
    }).GET($merge({
        title: title,
        parentId: where == 'inside' ? current.data.id : 0
      }, data));
  },

  storeTreeData: function(data) {
    if (!this.options.enableStorage) return;
    Ngn.localStorage.json.set(this.options.id + 'tree', data);
  },

  restoreState: function(data) {
    if (this.stateStorage) this.stateStorage.restore();
  },

  getStoredTreeData: function() {
    if (!this.options.enableStorage) return false;
    return Ngn.localStorage.json.get(this.options.id + 'tree');
  },

  removeStoredTreeData: function() {
    if (!this.options.enableStorage) return false;
    return Ngn.localStorage.removeItem(this.options.id + 'tree');
  },

  // выполняется в самом начале один раз
  initLoadEvents: function() {
    this.addEvent('dataLoad', function() {
      this.restoreState();
    });
    this.tree.addEvent('load', function(data) {
      this.fireEvent('dataLoad', data);
      this.storeTreeData(data);
      this.loading(false);
    }.bind(this));
  },

  loadFromStorage: function() {
    return false; // temporary disabled
    var data = this.getStoredTreeData();
    if (data) {
      this.tree.load({ json: data });
      this.fireEvent('dataLoad', data);
      return true;
    }
    return false;
  },

  load: function() {
    this.loading(true);
    this.tree.load({
      url: this.url + '?a=json_getTree'
    });
  },

  reload: function() {
    this.loading(true);
    this.tree.reload({
      url: this.url + '?a=json_getTree'
    });
  },

  toggleAll: function(state) {
    if (this.eBtnToggle) {
      if (state) {
        this.eBtnToggle.set('html', '<i></i>Свернуть все');
        this.eBtnToggle.removeClass('expand');
        this.eBtnToggle.addClass('collapse');
      } else {
        this.eBtnToggle.set('html', '<i></i>Развернуть все');
        this.eBtnToggle.removeClass('collapse');
        this.eBtnToggle.addClass('expand');
      }
      this.eBtnToggle.set('title', title);
    }
    this._toggleAll(state);
  },

  _toggleAll: function(state) {
    this.toggleState = state;
    this.tree.root.recursive(function() {
      this.toggle(state);
    });
  },

  openFirstRoot: function() {
    var ch = this.tree.root.getChildren();
    if (!ch[0]) return;
    ch[0].toggle(true);
  },

  buttonEls: {},
  buttons: {},
  activeIfNodeSelected: [],

  createButton: function(name, eBtn, func) {
    if (!eBtn) return;
    this.buttons[name] = new Ngn.Btn(eBtn, func);
  },

  bindButtons: function() {
    if (!this.eButtons) return false;
    this.createButton('add', this.eButtons.getElement('a[class~=add]'), this.add.bind(this));
    this.createButton('rename', this.eButtons.getElement('a[class~=rename]'), function() {
      if (!this.tree.selected.parentNode) return; // значит это дерево, а не нод
      if (this.tree.selected) this.tree.selected.rename();
    }.bind(this));
    this.createButton('delete', this.eButtons.getElement('a[class~=delete]'), function() {
      if (this.tree.selected) this.remove();
    }.bind(this));
    this.createButton('toggle', this.eButtons.getElement('a[class~=toggle]'), function() {
      this.toggleAll(!this.toggleState);
    }.bind(this));
    return true;
  },

  toggleButton: function(name, flag) {
    if (!$defined(this.buttons[name])) return;
    this.buttons[name].toggleDisabled(flag);
  },

  toggleButtons: function() {
    for (var name in this.buttons) this.toggleButton(name, false);
    this.tree.addEvent('selectChange', function(node) {
      // Включаем, если существует выбранный нод
      for (var i = 0; i < this.options.activeIfSelected.length; i++)
        this.toggleButton(this.options.activeIfSelected[i], this.tree.selected ? true : false);
    }.bind(this));
  },

  switchButtons: function(flag) {
    for (var i in this.buttons) this.buttons[i].toggleDisabled(flag);
  },

  /**
   * @todo Переместить в Dropdowner
   */
  rebuildButtons: function() {
    if (this.buttonsWidth > this.eButtons.getSizeWithoutPadding().x - this.buttonWidth) {
      var btns = this.eButtons.getElements('.iconBtn:not(.hidden,.down)');
      btns[btns.length - 1].addClass('hidden');
      Elements.from('<a href="#" class="iconBtn down"><i></i></a>')[0].inject(this.eButtons.getElement('.clear'), 'before');
      this.initButtonsWidth();
    }
  },

  /**
   * @todo Переименовать метод
   */
  initCollapseButtons: function() {
    this.buttonWidth = this.eButtons.getElement('.iconBtn').getSizeWithMargin().x;
    this.initButtonsWidth();
    (function() {
      this.rebuildButtons();
    }).delay(100, this);
  },

  initButtonsWidth: function() {
    this.buttonsWidth = 0;
    var btns = this.eButtons.getElements('.iconBtn:not(.hidden)');
    for (var i = 0; i < btns.length; i++) this.buttonsWidth += btns[i].getSizeWithMargin().x;
  },

  initUrl: function() {
    if (!this.options.actionUrl) throw new Ngn.EmptyError('this.options.actionUrl');
    this.url = this.options.actionUrl;
  },

  getContextMenu: function() {
    return false;
  }

});

