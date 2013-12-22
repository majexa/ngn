Ngn.Form.El.DdMetroMultiselect = new Class({
  Extends: Ngn.Form.El.DdTagsTreeMultiselectDialogable,

  options: {
    selectText: 'Выбрать станцию метро'
  },

  dialog: function() {
    return Ngn.Dialog.DdMetro;
  },

  init: function() {
    this.parent();
  }

});
