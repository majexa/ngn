Ngn.DdoItemsEdit = new Class({
  Implements: [Options],

  options: {
    baseUrl: ''
  },

  initialize: function(options) {
    //this.setOptions(options);
    if (Ngn.authorized) {
      document.getElements('.ddItems .item').each(function(eItem) {
        if (!Ngn.isAdmin && eItem.get('data-userId') != Ngn.authorized) return;
        var id = eItem.get('data-id');
        var eBtns = new Element('div', {'class': 'btns'}).inject(eItem, 'top');
        Ngn.Btn.btn2('Редактировать', 'edit').inject(eBtns).addEvent('click', function() {
          new Ngn.Dialog.RequestForm({
            title: 'Редактирование вещи',
            width: 300,
            url: this.options.baseUrl + '/?a=json_edit&id=' + id,
            onOkClose: function() {
              this.reloadItem(id);
            }.bind(this)
          });
        }.bind(this));
        Ngn.Btn.btn2('Удалить', 'delete').inject(eBtns).addEvent('click', function() {
          new Ngn.Dialog.Confirm.Mem({
            id: 'ddoItemsDelete',
            notAskSomeTime: true,
            onOkClose: function() {
              new Ngn.Request({
                url: this.options.baseUrl + '/?a=ajax_delete&id=' + id,
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
      }.bind(this));
    }
  },
  reloadItem: function(id) {
  }

});