Ngn.MultiImageUploadWizardForm = new Class({
  Extends: Ngn.Form,

  submitedAndUploaded: function(r) {
    if (!r.result || !r.result.itemsAddForm) throw new Error('no itemsAddForm in result');
    var eItems = $('ddItemsAdd');
    eItems.set('html', '');
    Ngn.Form.factory(Elements.from(r.result.itemsAddForm)[0].inject(eItems).getElement('form'));
    new Fx.Morph(this.eForm, {
      onComplete: function() {
        this.eForm.getParent('.blockUpload').getElement('h2').set('html', 'Вещи загружены.<br>Перейдите к добавлению');
        this.eForm.dispose();
      }.bind(this)
    }).start({opacity: 0});
  }

});
