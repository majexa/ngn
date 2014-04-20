Ngn.Dialog.VResize.Wisiwig = new Class({
  Extends: Ngn.Dialog.VResize,

  getResizebleEl: function() {
    return this.dialog.eMessage.getElement('iframe');
  }

});
