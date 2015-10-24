Ngn.Dialog.Textarea = new Class({
  Extends: Ngn.Dialog,

  initialize: function(_opts) {
    _opts.dialogClass = 'dialog dialog-textarea dialog-nopadding';
    var opts = Object.merge(_opts, {
      ok: this.closeAction.bind(this),
      bindBuildMessageFunction: true
    });
    this.parent(opts);
  },

  buildMessage: function(_msg) {
    this.id = 'textarea_' + this.options.id;
    this.eTextarea = new Element('textarea', {
      'id': this.id,
      'text': _msg
    });
    return this.eTextarea;
  },

  closeAction: function(_canceled) {
    this.options.callback(this.eTextarea.get('value'));
  }

});
