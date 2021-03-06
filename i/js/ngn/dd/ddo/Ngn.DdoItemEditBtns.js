Ngn.DdoItemEditBtns = new Class({
  Implements: [Options, Events],
  
  options: {
    baseUrl: window.location.pathname,
    movable: false
  },

  initialize: function(id, btnsContainer, options) {
    this.id = id;
    this.btnsContainer = btnsContainer;
    this.setOptions(options);
    this.init();
  },

  init: function() {
    //if (this.options.movable) {
    //  Ngn.Btn.btn2('Пеедвинуть', 'drag').inject(this.btnsContainer).addEvent('click', function() {
    //    // ...
    //  }.bind(this));
    //}
    Ngn.Btn.btn2('Редактировать', 'edit').inject(this.btnsContainer).addEvent('click', function() {
      new Ngn.Dialog.RequestForm({
        title: 'Редактирование',
        width: 300,
        url: this.options.baseUrl + '?a=json_edit&id=' + this.id,
        onOkClose: function() {
          this.fireEvent('editComplete', this.id);
        }.bind(this)
      });
    }.bind(this));
    Ngn.Btn.btn2('Удалить', 'delete').inject(this.btnsContainer).addEvent('click', function() {
      new Ngn.Dialog.Confirm.Mem({
        id: 'ddoItemsDelete',
        notAskSomeTime: true,
        onOkClose: function() {
          new Ngn.Request({
            url: this.options.baseUrl + '?a=ajax_delete&id=' + this.id,
            onComplete: function() {
              this.fireEvent('deleteComplete');
            }.bind(this)
          }).send();
        }.bind(this)
      });
    }.bind(this));
  }

});
