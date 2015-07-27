Ngn.DdoItemEditBtns = new Class({
  Implements: [Options, Events],
  
  options: {
    baseUrl: window.location.pathname
  },

  initialize: function(id, btnsContainer, options) {
    this.id = id;
    this.btnsContainer = btnsContainer;
    this.setOptions(options);
    this.init();
  },

  init: function() {
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
              new Fx.Morph(eBtns, {
                duration: 200
              }).start({opacity: 0});
              new Fx.Morph(eItem, {
                onComplete: function() {
                  eItem.destroy();
                }
              }).start({
                  width: 0,
                  'margin-right': 0,
                  opacity: 0
                });
            }.bind(this)
          }).send();
        }.bind(this)
      });
    }.bind(this));
  }

});
