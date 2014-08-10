Ngn.ElSelectDialog = new Class({
  Extends: Ngn.Dialog,
  options: {
    dialogClass: 'dialog selectDialog',
    noPadding: false
  },
  okClose: function() {
    //this.formEl.setVisibleValue(this.getValue());
    this.fireEvent('changeValue', this.getValue());
    this.parent();
  },
  getValue: function() {
    throw new Error('Abstract');
  }
});