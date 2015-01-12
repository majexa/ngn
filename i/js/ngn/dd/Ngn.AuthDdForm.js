Ngn.AuthDdForm = new Class({
  Extends: Ngn.DdForm,
  submit: function() {
    if (!this.validator.validate()) return false;
    var form = this;
    new Ngn.Dialog.Auth({
      reloadOnAuth: false,
      onAuthComplete: function() {
        form.fireEvent('submit');
        form.disable(true);
        form.submitAjax();
      }.bind(this)
    });
  }
});
