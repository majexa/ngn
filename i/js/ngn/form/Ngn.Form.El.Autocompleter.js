Ngn.Form.El.Autocompleter = new Class({
  Extends: Ngn.Form.El,

  init: function() {
    new Ngn.Form.El.Autocompleter.Autocompleter(this.eRow.getElement('input.fld'), this.form.options.dialog ? {zIndex: this.form.options.dialog.options.baseZIndex + 10 } : {});
  }

});
