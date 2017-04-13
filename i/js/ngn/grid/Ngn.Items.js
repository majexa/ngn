Ngn.Items = new Class({
  Implements: [Options, Events],

  options: {
    idParam: 'id',
    mainElementSelector: '.mainContent',
    eItems: '#items',
    itemElementSelector: '.item',
    deleteAction: 'delete',
    isSorting: false,
    itemsLayout: 'details',
    reloadOnDelete: false,
    disableInit: false
  },

  initialize: function (options) {
    this.setOptions(options);
    this.options.itemDoubleParent = this.options.itemsLayout == 'tile' ? false : true;
    if (!this.options.disableInit) this.init();
    return this;
  },

  init: function () {
    this.initItems();
  },

  getId: function (eItem) {
    if (!eItem.get('id')) console.debug(eItem);
    return eItem.get('id').split('_')[1];
  },

  toolBtnAction: function (cls, action) {
    for (var i = 0; i < this.esItems.length; i++) {
      var id = this.getId(this.esItems[i]);
      Ngn.addBtnAction('.tools a[.' + cls + ']', action.pass(id), this.esItems[i]);
    }
  },

  initItems: function () {
    this.eItems = document.getElement(this.options.eItems);
    var esItems = this.eItems.getElements(this.options.itemElementSelector);
    this.esItems = {};
    for (var i = 0; i < esItems.length; i++) {
      var id = this.getId(esItems[i]);
      this.esItems[id] = esItems[i];
      this.esItems[id].store('itemId', id);
    }
    this.initToolActions();
  },

  loading: function (id, flag) {
    if (!this.esItems[id]) return;
    flag ? this.esItems[id].addClass('loading') : this.esItems[id].removeClass('loading');
  },

  reload: function () {
    Ngn.Request.Iface.loading(true);
    new Request({
      url: window.location.pathname + '?a=ajax_reload',
      onComplete: function (html) {
        this.eItems.empty();
        this.eItems.set('html', html);
        this.init();
        Ngn.cp.initTooltips();
        Ngn.Request.Iface.loading(false);
        this.fireEvent('reloadComplete');
      }.bind(this)
    }).send();
  }

});