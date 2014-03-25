Ngn.frm.DdItemSelectDepending = new Class({
  Extends: Ngn.frm.ConsecutiveSelect,

  url: function() {
    return '/c/ddItemSelectDepending/?fieldName=' + this.options.fieldName + '&strName=' + this.options.strName + '&parentTagFieldName=' + this.options.parentTagFieldName + '&itemsSort=' + this.options.itemsSort;
  }

});