Ngn.Form.El.DdTagsConsecutiveSelect = new Class({
  Extends: Ngn.Form.El.Dd,

  init: function() {
    new Ngn.frm.DdConsecutiveSelect(this.eRow, this.strName, {
      onRequest: function(eSelect) {
        this.form.validator.resetField(eSelect);
      }.bind(this),
      onComplete: function() {
        this.form.validator.rewatchFields();
      }.bind(this)
    });
  }

});
