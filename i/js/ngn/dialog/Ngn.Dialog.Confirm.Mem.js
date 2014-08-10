Ngn.Dialog.Confirm.Mem = new Class({
  Extends: Ngn.Dialog.Confirm,

  options: {
    width: 250,
    okText: 'Удалить',
    bindBuildMessageFunction: true,
    notAskSomeTime: false
  },

  timeoutId: null,

  initialize: function(_opts) {
    this.setOptions(_opts);
    this.options.dialogClass += ' dialog-confirm';
    if (this.options.notAskSomeTime) {
      if (this.timeoutId) clearTimeout(this.timeoutId);
      this.timeoutId = (function() {
        Ngn.storage.remove(this.options.id + 'confirmMem');
      }).delay(120000, this);
    }
    if (Ngn.storage.get(this.options.id + 'confirmMem')) {
      this.fireEvent('okClose');
      return;
    }
    this.parent(_opts);
  },

  buildMessage: function(_msg) {
    var eMessageCont = new Element('div');
    if (this.options.notAskSomeTime) {
      var checkboxCaption = 'Неспрашивать меня об этом какое-то время';
    } else {
      var checkboxCaption = 'Больше не спрашивать по этому поводу';
    }
    new Element('div', {'html': '<h3 style="margin-top:0px">' + _msg + '</h3>'}).inject(eMessageCont);
    Elements.from('<span class="checkbox"><input type="checkbox" id="confirmMem' + this.options.id + '" class="confirmMem" /><label for="confirmMem' + this.options.id + '">' + checkboxCaption + '</label></span>')[0].inject(eMessageCont);
    this.eMemCheckbox = eMessageCont.getElement('.confirmMem');
    return eMessageCont;
  },

  finishClose: function() {
    if (this.isOkClose) {
      c([this.options.id + 'confirmMem', this.eMemCheckbox.get('checked')]);
      Ngn.storage.set(this.options.id + 'confirmMem', this.eMemCheckbox.get('checked'));
    }
    this.parent();
  }

});
