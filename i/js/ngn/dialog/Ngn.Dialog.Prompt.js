Ngn.Dialog.Prompt = new Class({
  Extends: Ngn.Dialog,

  initialize: function(_opts) {
    var opts = $merge(_opts, {
      cancel: false,
      titleClose: false,
      bindBuildMessageFunction: true,
      ok: this.closeAction.bind(this),
      cancel: this.closeAction.bind(this, false),
      onComplete: function() {
        var text_elem = this.dialogId + '_prompted';
        window.setTimeout(function() {
          $(text_elem).focus();
        }, 310);
      }
    });
    this.parent(opts);
  },

  buildMessage: function(_msg) {
    var message_box = new Element('div');
    new Element('div', {'class': 'icon-button prompt-icon goleft'}).inject(message_box);
    var msg_display = new Element('div', {'class': 'mav-alert-msg goleft'}).inject(message_box);

    new Element('div', {'html': _msg}).inject(msg_display);
    new Element('input', {
      'id': this.dialogId + '_prompted',
      'type': 'text',
      'class': 'mav-prompt-input'
    }).inject(msg_display);

    new Element('div', {'class': 'clear'}).inject(message_box);

    return message_box;
  },

  closeAction: function(_canceled) {
    this.close();

    var prompt_value = (_canceled === false ? null : $(this.dialogId + '_prompted').get('value'));
    if (this.options.useFx && $defined(this.options.callback)) {
      // bah.
      this.fx.start('opacity', 1, 0).chain(this.finishClose.bind(this)).chain(this.options.callback(prompt_value));
    } else {
      this.finishClose();
      if ($defined(this.options.callback) && $type(this.options.callback) == 'function') {
        this.options.callback(prompt_value);
      }
    }
  }
});
