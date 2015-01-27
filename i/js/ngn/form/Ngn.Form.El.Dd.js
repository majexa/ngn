Ngn.Form.El.Dd = new Class({
  Extends: Ngn.Form.El,

  initialize: function(type, form, eRow) {
    if (!form.strName) throw new Error('"strName" property not defined in form object');
    this.strName = form.strName;
    this.parent(type, form, eRow);
  }

});
