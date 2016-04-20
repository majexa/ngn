Ngn.Form.El.Textarea = new Class({
  Extends: Ngn.Form.El,

  resizebleOptions: {},

  init: function() {
    if (this.form.options.dialog && this.form.options.dialog.options.vResize) return;
    //new Ngn.ResizableTextarea(this.eRow); // реализовать настройку в Ngn.Form.ElInit...
  }

});
