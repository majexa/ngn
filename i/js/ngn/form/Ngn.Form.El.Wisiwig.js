Ngn.Form.El.Wisiwig = new Class({
  Extends: Ngn.Form.El.Textarea,

  resizebleOptions: {
    resizableElementSelector: 'iframe'
  },

  init: function() {
    this.form.options.dialog.setWidth(500);
    var eCol = this.eRow.getParent('.type_col');
    Ngn.whenElPresents(this.eRow, '.mceLayout', function(eMceLayout) {
      if (!eCol) return;
      var eColBody = eCol.getElement('.colBody');
      if (eColBody.getSize().x < eMceLayout.getSize().x) eColBody.setStyle('width', eMceLayout.getSize().x + 'px');
      if (this.form.options.dialog) this.form.options.dialog.resizeByCols();
      // Если высота всех элементов колонки меньше
      var colH = eCol.getParent('.colSet').getSize().y;
      var els = eCol.getElements('.element');
      var allColElsH = 0;
      for (var i = 0; i < els.length; i++) allColElsH += els[i].getSize().y;
    }.bind(this));
  }

});