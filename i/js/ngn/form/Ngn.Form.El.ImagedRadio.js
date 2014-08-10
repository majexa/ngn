Ngn.Form.El.ImagedRadio = new Class({
  Extends: Ngn.Form.El,

  init: function() {
    var elements = this.eRow.getElements('.radio');
    if (!elements.length) return;
    var items = [], n = 0, selectedN = false;
    elements.each(function(eWrapper) {
      var eLabel = eWrapper.getElement('label');
      eLabel.set('html', eLabel.get('html').substr(0, 10));
      var eInput = eWrapper.getElement('input');
      items[n] = [ eWrapper, eInput ];
      eWrapper.store('n', n);
      if (eInput.get('checked')) {
        eWrapper.addClass('selected');
        selectedN = n;
      }
      eWrapper.addEvent('click', function(e) {
        e.preventDefault();
        var nn = this.retrieve('n');
        if (selectedN !== false) {
          items[selectedN][0].removeClass('selected');
          items[selectedN][1].set('checked', false);
        }
        selectedN = nn;
        this.addClass('selected');
        items[nn][1].set('checked', true);
      });
      n++;
    }.bind(this));
  }

});