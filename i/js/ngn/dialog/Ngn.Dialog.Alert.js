Ngn.Dialog.Alert = new Class({
  Extends: Ngn.Dialog,

  options: {
    noPadding: false,
    title: false
  },

  initialize: function(_opts) {
    var opts = $merge(_opts, {
      cancel: false,
      titleClose: false,
      bindBuildMessageFunction: true
    });
    this.parent(opts);
  },

  buildMessage: function(msg) {
    var message_box = new Element('div');
    new Element('div', {'class': 'icon-button alert-icon goleft'}).inject(message_box);
    new Element('div', {'class': 'mav-alert-msg goleft', 'html': msg}).inject(message_box);
    new Element('div', {'class': 'clear'}).inject(message_box);
    return message_box;
  }
});
