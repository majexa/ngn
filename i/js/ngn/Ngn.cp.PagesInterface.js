Ngn.cp.ItemsTablePages = new Class({
  Extends: Ngn.Items.Table.Pages,
  
  initialize: function(twoScreens, options) {
    this.twoScreens = twoScreens;
    this.parent(options);
  },
  
  init: function() {
    this.parent();
    this.addBtnsActions([
      ['a.editProp', this.twoScreens.openPagePropDialog.bind(this)],
      ['a.editControllerSettings', this.twoScreens.openControllerSettingsDialog.bind(this)]
    ]);
  }
  
});


Ngn.cp.PagesInterface = new Class({

  initialize: function() {
    this.eHandler = $('handler');
    this.eSubNav = $('subNav');
    if ($('treeContainer')) {
      this.tree = new Ngn.TreeEdit.Pages.cp(
        this,
        'treeContainer',
        {
          buttons: 'treeMenu',
          onTreeLoad: function(){
            this.toggleAll(true);
          }
        }
      ).init();
    }
    if ($('mainContent').hasClass('a_default')) this.initPagesList();
    this.initPanels();
    this.addSubNavBtnAction('a.createModulePage', this.openModuleCreateDialog.pass(parseInt(Ngn.getParam(2, true)), this));
    this.addSubNavBtnAction('a.createPage', this.openCreateDialog.pass(parseInt(Ngn.getParam(2, true)), this));
    this.addSubNavBtnAction('a.editProp', this.openPagePropDialog.pass(Ngn.getParam(2), this));
    this.eSubNav.getElements('a.editOptions').each(function(eBtn) {
      this._addSubNavBtnAction(
        eBtn,
        this.openControllerSettingsDialog.pass(Ngn._getParam(eBtn.get('href'), 2), this)
      );
    }.bind(this));
    this.addSubNavBtnAction('a.add', this.openNewDdItemDialog.bind(this), true);
  },
  
  addSubNavBtnAction: function(selector, action, passBtnElement) {
    var eBtn = this.eSubNav.getElement(selector);
    if (!eBtn) return;
    if (passBtnElement) action = action.pass(eBtn);
    this._addSubNavBtnAction(eBtn, action);
  },
  
  _addSubNavBtnAction: function(eBtn, action) {
    eBtn.addEvent('click', function(e) {
      e.preventDefault();
      action();
    });
  },
  
  initPanels: function() {
    if ($('ddFilters')) {
      var opt = {
        storeId: 'pagesInterface',
        rightExcludeEls: [this.eSubNav]
      };
      if (this.tree) opt.leftExcludeEls = [this.tree.eButtons, this.tree.container];
      new Ngn.cp.TwoPanels($('ddFilters'), $('rightPanel'), this.eHandler, opt);
    } else if (this.tree) {
      new Ngn.cp.TwoPanels(this.tree.container, $('rightPanel'), this.eHandler, {
        storeId: 'pagesInterface',
        leftExcludeEls: [this.tree.eButtons],
        rightExcludeEls: [this.eSubNav]
      });
    } else {
      //throw new Error('cant initialize two panels');
    }
  },
  
  openModuleCreateDialog: function(pageId) {
    new Ngn.Dialog.NewModulePage({
      pageId: pageId,
      onSubmitSuccess: function() {
        this.tree.reload();
        //this.reloadItemsList();
      }.bind(this)
    });
  },
  
  openCreateDialog: function(pageId) {
    new Ngn.Dialog.NewPage({
      pageId: pageId,
      onSubmitSuccess: function() {
        this.tree.reload();
        this.reloadItemsList();
      }.bind(this)
    });
  },

  openPagePropDialog: function(pageId) {
    new Ngn.Dialog.RequestForm({
      title: 'Редактирование параметров раздела',
      url: Ngn.getPath(2) + '/' + pageId + '/json_editPageProp',
      onSubmitSuccess: function() {
        if (this.tree) this.tree.reload();
        this.reloadItemsList();
      }.bind(this)
    });
  },
  
  openControllerSettingsDialog: function(pageId) {
    new Ngn.Dialog.Queue.Request({
      url: Ngn.getPath(2) + '/' + pageId + '?a=json_editControllerSettings',
      width: 600,
      reduceHeight: true
    });
  },
  
  openNewDdItemDialog: function(eBtn) {
    new Ngn.Dialog.RequestForm({
      title: false,
      reduceHeight: true,
      url: eBtn.get('href').replace('new', 'json_new'),
      onSubmitSuccess: function() {
        window.location.reload(true);
      }
    });
  },
  
  initPagesList: function() {
    if (!$('itemsTable')) return;
    this.itemsList = new Ngn.cp.ItemsTablePages(this, {
      onDeleteComplete: function() {
        if (this.tree) this.tree.reload();
      }.bind(this),
      onActiveChangeComplete: function() {
        if (this.tree) this.tree.reload();
      }.bind(this),
      onMenuChangeComplete: function() {
        if (this.tree) this.tree.reload();
      }.bind(this),
      onMoveComplete: function() {
        if (this.tree) this.tree.reload();
      }.bind(this)
    });
    if (this.tree) this.tree.addEvent('deleteComplete', function(node) {
      this.itemsList.reload();
    }.bind(this));
    if (this.tree) this.tree.addEvent('renameComplete', function(node) {
      this.itemsList.reload();
    }.bind(this));
    if (this.tree) this.tree.addEvent('moveComplete', function(node) {
      this.itemsList.reload();
      this.tree.reload();
    }.bind(this));
  },
  
  reloadItemsList: function() {
    if (!this.itemsList) return;
    this.itemsList.reload();
  }
  
});
