Ngn.cp.DdItemsTable = new Class({
  Extends: Ngn.Items.Table,
  
  options: {
    idParam: 'itemId'
  },
  
  init: function() {
    this.parent();
    this.addBtnsActions([
      ['a.editDate', function(itemId) {
        new Ngn.Dialog.RequestForm({
          url: Ngn.Url.getPath(3)+'/json_editItemSystemDates/'+itemId
        });
      }]
    ]);
  }
  
});