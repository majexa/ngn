Ngn.Frm.DdConsecutiveSelect = new Class({
  Extends: Ngn.Frm.ConsecutiveSelect,

  initialize: function(eParent,options) {
    this.strName = options.strName;
    this.parent(eParent, options.strName, options);
  },

  url: function() {
    return '/c/ddTagsConsecutiveSelect/' + this.strName;
  }
  
});