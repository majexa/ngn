Ngn.Dialog.DdMetro = new Class({
  Extends: Ngn.Dialog.DdTagsTreeMultiselectDialog,
  options: {
    width: 400,
    title: 'Выберите несколько удобных станций метро'
    //textInfo: 'Выберите наиболее удобные для вас станции метро из списка:'
  }
});

Ngn.Form.El.DdMetro = new Class({
  //Extends: Ngn.Form.El.DdTagsTreeMultiselectDialogable,
  Extends: Ngn.Form.El.DdTagsTreeMultiselect,

  options: {
    selectText: 'Выбрать станцию метро'
  },

  dialog: function() {
    return Ngn.Dialog.DdMetro;
  }

});
