Ngn.Dialog.Error = new Class({
  Extends: Ngn.Dialog.Alert,

  options: {
    title: 'Ошибка',
    width: 600
  },

  buildMessage: function(msg) {
    //throw new Error(this.options.error.message);
    //return this.parent('<p>' + this.options.error.message + ' <i>Code: ' + this.options.error.code + '</i></p>' + '<p>' + this.options.error.trace + '</p>');
  }

});
