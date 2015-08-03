Ngn.AuthEditItems = new Class({
  Implements: [Options],

  options: {
    baseUrl: window.location.pathname,
    esItems: []
  },

  initialize: function(options) {
    this.setOptions(options);
    if (Ngn.authorized) {
      this.initBtns();
    }
  },

  initBtns: function() {
    c('888');
    var esItems = typeof(this.options.items) == 'object' ? //
      this.options.items : //
      document.getElements(this.options.items);
    esItems.each(function(eItem) {
      if (!Ngn.isAdmin && eItem.get('data-userId') != Ngn.authorized) return;
      this.initItem(eItem);
    }.bind(this));
  },

  initItem: function(id) {}

});