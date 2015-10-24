Ngn.Dialog.Iframe = new Class({
  Extends: Ngn.Dialog,

  options: {
    // iframeStyles: {}
    okDestroy: false
  },

  iframeStyles: {
    'border': '0px',
    'width': '100%'
  },

  initialize: function(_opts) {
    //_opts.dialogClass = 'dialog dialog-textarea dialog-nopadding';
    var opts = Object.merge(_opts, {
      ok: this.okAction.bind(this),
      bindBuildMessageFunction: true
    });
    opts.iframeStyles = Object.merge({
      'border': '0px',
      'width': '100%',
      'height': '100%'
    }, opts.iframeStyles);
    this.parent(opts);
  },

  buildMessage: function() {
    this.eIframe = new Element('iframe', {
      'src': this.options.iframeUrl,
      'styles': this.options.iframeStyles
    });
    return this.eIframe;
  },

  okAction: function() {
    if (this.eIframe.contentWindow.okAction()) this.okClose();
  }

});
