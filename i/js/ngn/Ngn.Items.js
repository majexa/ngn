Ngn.Items = new Class({
  Implements: [Options, Events],
  
  options: {
    idParam: 'id',
    mainElementSelector: '.mainContent',
    eItems: 'items',
    itemElementSelector: '.item',
    deleteAction: 'delete',
    isSorting: false,
    itemsLayout: 'details',
    reloadOnDelete: false,
    disableInit: false
  },
  
  initialize: function(options) {
    this.setOptions(options);
    this.options.itemDoubleParent = this.options.itemsLayout == 'tile' ? false : true;
    if (!this.options.disableInit) this.init();
    return this;
  },

  init: function() {
    this.initItems();
  },
  
  getId: function(eItem) {
    if (!eItem.get('id')) c(eItem);
    return eItem.get('id').split('_')[1];
  },

  toolBtnAction: function(cls, action) {
    for (var i=0; i<this.esItems.length; i++) {
      var id = this.getId(this.esItems[i]);
      Ngn.addBtnAction('.tools a[.'+cls+']', action.pass(id), this.esItems[i]);
    }
  },
  
  initItems: function() {
    this.eItems = $(this.options.eItems);
    var esItems = this.eItems.getElements(this.options.itemElementSelector);
    this.esItems = {};
    for (var i=0; i<esItems.length; i++) {
      var id = this.getId(esItems[i]);
      this.esItems[id] = esItems[i];
      this.esItems[id].store('itemId', id);
    }
    this.initToolActions();
  },

  loading: function(id, flag) {
    if (!this.esItems[id]) return;
    flag ? this.esItems[id].addClass('loading') : this.esItems[id].removeClass('loading');
  },

  initToolActions: function() {
    this.addBtnsActions([
      ['.delete', function(id, eBtn, eItem) {
        new Ngn.Dialog.Confirm.Mem({
          id: 'itemsDelete',
          notAskSomeTime: true,
          onOkClose: function() {
            this.loading(id, true);
            var g = {};
            g[this.options.idParam] = id;
            new Request({
              url: this.getLink() + '?a=ajax_' + this.options.deleteAction,
              onComplete: function() {
                this.options.reloadOnDelete ? this.reload() : eItem.destroy();
              }.bind(this)
            }).GET(g);
          }.bind(this)
        });
      }.bind(this)],
      ['a[class~=flagOn],a[class~=flagOff]', function(id, eBtn) {
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

  _addBtnAction: function(eItem, selector, action) {
    if (!eItem) return;
    var eBtn = eItem.getElement(selector);
    if (!eBtn) return;
    eBtn.addEvent('click', function(e){
      e.preventDefault();
      action(eItem.retrieve('itemId'), eBtn, eItem);
    }.bind(this));
  },

  addBtnAction: function(selector, action) {
    Object.every(this.esItems, function(eItem) {
      this._addBtnAction(eItem, selector, action);
    }.bind(this));
  },
  
  addBtnsActions: function(actions) {
    for (var i in this.esItems) {
      var eItem = this.esItems[i];
      for (var j=0; j<actions.length; j++) {
        this._addBtnAction(eItem, actions[j][0], actions[j][1]);
      }
    }
  },
  
  reload: function() {
    Ngn.loading(true);
    new Request({
      url: window.location.pathname + '?a=ajax_reload',
      onComplete: function(html) {
        this.eItems.empty();
        this.eItems.set('html', html);
        this.init();
        Ngn.cp.initTooltips();
        Ngn.loading(false);
        this.fireEvent('reloadComplete');
      }.bind(this)
    }).send();
  }

});