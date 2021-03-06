Ngn.Dialog.Confirm.Mem = new Class({
  Extends: Ngn.Dialog.Confirm,

  options: {
    width: 250,
    okText: Ngn.Locale.get('core.delete'),
    bindBuildMessageFunction: true,
    notAskSomeTime: false,
    title: false
  },

  timeoutId: null,

  initialize: function(_opts) {
    if (_opts) _opts.title = false; // force title disabling
    this.setOptions(_opts);
    this.options.dialogClass += ' dialog-confirm';
    if (this.options.notAskSomeTime) {
      if (this.timeoutId) clearTimeout(this.timeoutId);
      this.timeoutId = (function() {
        Ngn.Storage.remove(this.options.id + 'confirmMem');
      }).delay(120000, this);
    }
    if (Ngn.Storage.get(this.options.id + 'confirmMem')) {
      this.fireEvent('okClose');
      return;
    }
    this.parent(_opts);
  },

  buildMessage: function(_msg) {
    var eMessageCont = new Element('div'), checkboxCaption;
    if (this.options.notAskSomeTime) {
      checkboxCaption = Ngn.Locale.get('core.doNotAskMeSomeTimeAboutThat');
    } else {
      checkboxCaption = Ngn.Locale.get('core.doNotAskMeAnymoreAboutThat');
    }
    new Element('div', {'html': '<h3 style="margin-top:0px">' + _msg + '</h3>'}).inject(eMessageCont);
    Elements.from('<span class="checkbox"><input type="checkbox" id="confirmMem' + this.options.id + '" class="confirmMem" /><label for="confirmMem' + this.options.id + '">' + checkboxCaption + '</label></span>')[0].inject(eMessageCont);
    this.eMemCheckbox = eMessageCont.getElement('.confirmMem');
    return eMessageCont;
  },

  finishClose: function() {
    if (this.isOkClose) {
      console.debug([this.options.id + 'confirmMem', this.eMemCheckbox.get('checked')]);
      Ngn.Storage.set(this.options.id + 'confirmMem', this.eMemCheckbox.get('checked'));
    }
    this.parent();
  }

});
