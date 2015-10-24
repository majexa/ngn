Ngn.Dialog.Wysiwyg = new Class({
  Extends: Ngn.Dialog,

  initialize: function(_opts) {
    var opts = Object.merge(_opts, {
      'ok': this.closeAction.bind(this),
      'bindBuildMessageFunction': true,
    });
    this.parent(opts);
  },

  buildMessage: function(_msg) {
    this.wysiwyg = new Wysiwyg({
      textarea: new Element('textarea', {
        'id': 'wisiwigDialog',
        'text': _msg
      }),
      css: this.options.css ? this.options.css : null
      //buttons: ['strong','em','u','superscript','subscript','ul','ol']
    });
    alert(this.wysiwyg.CT);
    return this.wysiwyg.CT;
  },

  closeAction: function(_canceled) {
    this.options.callback(this.wysiwyg.getHTML());
  }

});