Ngn.Form.El.Textarea = new Class({
  Extends: Ngn.Form.El,

  init: function() {
    if (this.form.options.dialog && this.form.options.dialog.options.vResize) return;
    new Ngn.ResizableTextarea(this.eRow);
  }

});
