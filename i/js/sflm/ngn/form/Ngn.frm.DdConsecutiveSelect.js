Ngn.frm.DdConsecutiveSelect = new Class({
  Extends: Ngn.frm.ConsecutiveSelect,

  initialize: function(eParent,options) {
    this.strName = options.strName;
    this.parent(eParent, options.strName, options);
  },

  url: function() {
    return '/c/ddTagsConsecutiveSelect/' + this.strName;
  }
  
});