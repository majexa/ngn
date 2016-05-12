Ngn.Dialog.Prompt = new Class({
  Extends: Ngn.Dialog,

  initialize: function(opts) {
    this.parent(Object.merge(opts, {
      titleClose: false,
      bindBuildMessageFunction: true,
      ok: this.closeAction.bind(this),
      cancel: this.closeAction.bind(this, false),
      onComplete: function() {
        window.setTimeout(function() {
          document.getElement(this.dialogId + '_prompted').focus();
        }, 310);
      }
    }));
  },

  buildMessage: function(_msg) {
    var eMessageBox = new Element('div');
    new Element('div', {'class': 'icon-button prompt-icon goleft'}).inject(eMessageBox);
    var eMsgDisplay = new Element('div', {'class': 'mav-alert-msg goleft'}).inject(eMessageBox);
    new Element('div', {'html': _msg}).inject(eMsgDisplay);
    new Element('input', {
      'id': this.dialogId + '_prompted',
      'type': 'text',
      'class': 'mav-prompt-input'
    }).inject(eMsgDisplay);
    new Element('div', {'class': 'clear'}).inject(eMessageBox);
    return eMessageBox;
  },

  closeAction: function(_canceled) {
    this.close();

    var prompt_value = (_canceled === false ? null : $(this.dialogId + '_prompted').get('value'));
    if (this.options.useFx && this.options.callback != undefined) {
      // bah.
      this.fx.start('opacity', 1, 0).chain(this.finishClose.bind(this)).chain(this.options.callback(prompt_value));
    } else {
      this.finishClose();
      if (this.options.callback != undefined && typeOf(this.options.callback) == 'function') {
        this.options.callback(prompt_value);
      }
    }
  }
});
