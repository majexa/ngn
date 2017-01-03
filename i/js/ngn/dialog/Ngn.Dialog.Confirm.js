// @requiresBefore s2/js/locale?key=core
Ngn.Dialog.Confirm = new Class({
  Extends: Ngn.Dialog.Msg,

  options: {
    width: 300,
    message: Locale.get('Core.areYouSure'),
    dialogClass: 'dialog dialog-confirm'
  },

  initialize: function(_opts) {
    var opts = Object.merge(_opts, {
      titleClose: false,
      ok: this.closeAction.bind(this, true),
      cancel: this.closeAction.bind(this, false)
    });
    this.parent(opts);
  },

  closeAction: function(_confirmed) {
    _confirmed ? this.okClose() : this.close();
  }

});
