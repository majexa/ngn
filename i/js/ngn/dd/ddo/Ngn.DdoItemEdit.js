Ngn.DdoItemEdit = new Class({
  Implements: [Options],

  options: {
    baseUrl: window.location.pathname,
    onEditComplete: null
  },

  initialize: function(id, eBtns, options) {
    this.setOptions(options);
    new Ngn.DdoItemEditBtns(id, eBtns, {
      baseUrl: this.options.baseUrl,
      onEditComplete: function(id) {
        if (this.options.onEditComplete) this.options.onEditComplete(id);
      }.bind(this)
    });
  }

});
