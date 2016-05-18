Ngn.Dialog.Loader = new Class({
  Extends: Ngn.Dialog,

  options: {
    bindBuildMessageFunction: true,
    ok: false,
    hasFaviconTimer: true // при редиректе, после включения DialogLoader'а FaviconTimer необходимо отключить
  },

  initialize: function(options) {
    this.parent(options);
  },

  start: function() {
    if (this.options.hasFaviconTimer) Ngn.FaviconTimer.start();
  },

  stop: function() {
    if (this.options.hasFaviconTimer) Ngn.FaviconTimer.stop();
  },

  close: function() {
    this.stop();
    this.parent();
  },

  buildMessage: function() {
    return '<div class="dialog-progress"></div>';
  }

});

Ngn.Dialog.Loader.Simple = new Class({
  Extends: Ngn.Dialog.Loader,

  options: {
    //cancel: false,
    titleClose: false,
    footer: false,
    messageBoxClass: 'dummy',
    titleBarClass: 'dialog-loader-title',
    titleTextClass: 'dummy',
    messageAreaClass: 'dummy',
    bindBuildMessageFunction: true
  }

});

Ngn.Dialog.Loader.Advanced = new Class({
  Extends: Ngn.Dialog.Loader,

  options: {
    messageAreaClass: 'dialog-message dialog-message-loader',
    onContinue: Function.from(),
    noPadding: false
  },

  init: function() {
    this.eProgress = this.message.getElement('.dialog-progress');
    this.stop();
  },

  buildMessage: function() {
    return '<div class="message-text"></div><div class="dialog-progress"></div>';
  },

  start: function() {
    this.eProgress.removeClass('stopped');
    this.parent();
  },

  stop: function() {
    this.eProgress.addClass('stopped');
    this.parent();
  }

});

Ngn.Dialog.Loader.Request = new Class({
  Extends: Ngn.Dialog.Loader.Simple,

  options: {
    loaderUrl: null,
    onLoaderComplete: Function.from(),
    titleClose: false,
    footer: false
  },

  initialize: function(options) {
    this.parent(options);
    new Request({
      url: this.options.loaderUrl,
      onComplete: function(r) {
        this.okClose();
        this.fireEvent('loaderComplete', r);
      }.bind(this)
    }).send();
  }

});