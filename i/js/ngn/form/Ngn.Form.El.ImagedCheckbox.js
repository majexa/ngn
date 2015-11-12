Ngn.Form.El.ImagedCheckbox = new Class({
  Extends: Ngn.Form.El,

  init: function() {
    var elements = this.eRow.getElements('.checkbox');
    if (!elements.length) return;
    elements.each(function(eWrapper) {
      eWrapper.addEvent('click', function(e) {
        var eInput = eWrapper.getElement('input');
        console.debug(eInput);
        console.debug(eInput.get('checked'));
        if (eInput.get('checked')) {
          eWrapper.addClass('selected');
          eInput.set('checked', true);
        } else {
          eWrapper.removeClass('selected');
          eInput.set('checked', false);
        }
      });
    }.bind(this));
  }

});