Ngn.CrudDialogNew = new Class({
  Extends: Ngn.CrudDialog,
  Implements: [Ngn.Dialog.RequestForm.Json.NoStartupRequest],
  options: {
    id: 'dlgNew',
    title: 'Создание',
  }
});
