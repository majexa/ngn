Ngn.FieldSet.Html = new Class({
  Extends: Ngn.FieldSet,

  getContainer: function() {
    return this.eContainerInit;
  },

  initialize: function(container, options) {
    this.eContainerInit = $(container);
    this.parent(this.eContainerInit.getParent(), options);
  }

});
