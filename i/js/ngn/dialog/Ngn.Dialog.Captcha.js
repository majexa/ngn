Ngn.Dialog.Captcha = new Class({
  Extends: Ngn.Dialog,

  options: {
    title: 'Введите код с картинки',
    bindBuildMessageFunction: true,
    dialogClass: 'dialog mav-center mav-captcha',
    width: 240,
    noPadding: false,
    okDestroy: false
  },

  initialize: function(opts) {
    var opts = Object.merge(opts, {
      ok: this.okAction.bind(this)
    });
    this.parent(opts);
    (function() {
      this.message.getElement('input').focus();
      this.captcha = this.message.getElement('.captcha');
      this.captchaHelp = this.message.getElement('.captchaHelp');
    }).delay(100, this);
  },

  buildMessage: function() {
    return Elements.from('<div><img src="/c/captcha/captcha" class="captcha" /><input id="keystring" type="text" /><div><small class="captchaHelp">регистр не важен</small></div></div>')[0];
  },

  okAction: function() {
    this.loading(true);
    new Request({
      url: 'c/captcha/ajax_check',
      onComplete: function(r) {
        this.loading(false);
        if (r == 'failed') {
          this.captchaHelp.set('html', 'Вы ввели неправвильный код');
          this.captchaHelp.addClass('error');
          this.captcha.set('src', '/c/captcha/captcha?' + Math.random());
        } else {
          this.okClose();
        }
      }.bind(this)
    }).post({
        keystring: $('keystring').get('value')
      });
    return false;
  }

});
