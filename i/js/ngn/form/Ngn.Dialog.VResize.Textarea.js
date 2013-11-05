Ngn.Dialog.VResize.Textarea = new Class({
  Extends: Ngn.Dialog.VResize,

  getResizebleEl: function() {
    return this.dialog.eMessage.getElement('textarea');
  }

});
