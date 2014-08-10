/**
 * @requires Meio.Mask
 */
Ngn.Form.ElInit.Phone = new Class({
  Extends: Ngn.Form.ElInit,

  init: function() {
    Meio.Mask.createMasks('Fixed', {
      'Phone-ru': {mask: '+7 999 999 99 99'}
    })
    this.parent();
  }

});

Ngn.Form.El.Phone = new Class({
  Extends: Ngn.Form.El,

  init: function() {
    if (Browser.Platform.android) return;
    this.eRow.getElement('input').meiomask("fixed.phone-ru");
  }

});