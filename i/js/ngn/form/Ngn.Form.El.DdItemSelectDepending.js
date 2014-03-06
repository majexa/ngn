Ngn.Form.El.DdItemSelectDepending = new Class({
  Extends: Ngn.Form.El.Dd,

  init: function() {
    var data = this.eRow.getElement('.data');
    this.parentTagFieldName = data.get('data-parentTagFieldName');
    this.fieldName = data.get('data-fieldName');
    this.strName = data.get('data-strName');
    Ngn.frm.ConsecutiveSelect.factory(this, Ngn.frm.DdItemSelectDepending);
  }

});