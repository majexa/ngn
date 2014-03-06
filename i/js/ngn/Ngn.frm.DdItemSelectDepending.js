Ngn.frm.DdItemSelectDepending = new Class({
  Extends: Ngn.frm.ConsecutiveSelect,

  initialize: function(eParent, fieldName, strName, parentTagFieldName, options) {
    this.fieldName = fieldName;
    this.strName = strName;
    this.parentTagFieldName = parentTagFieldName;
    this.parent(eParent, strName, options);
  },

  url: function() {
    return '/c/ddItemSelectDepending/?fieldName=' + this.fieldName + '&strName=' + this.strName + '&parentTagFieldName=' + this.parentTagFieldName;
  }

});