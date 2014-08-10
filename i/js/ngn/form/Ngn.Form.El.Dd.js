Ngn.Form.El.Dd = new Class({
  Extends: Ngn.Form.El,

  initialize: function(type, form, eRow) {
    if (!form.strName) throw new Error('form must be Ngn.DdForm instance');
    this.strName = form.strName;
    this.parent(type, form, eRow);
  }

});
