Ngn.DdoItemsEdit = new Class({
  Extends: Ngn.AuthEditItems,
  Implements: [Events],

  options: {
    items: '.ddItems .item'
  },

  initItem: function(eItem) {
    new Ngn.DdoItemEditBtns( //
      eItem.get('data-id'), //
      new Element('div', {'class': 'btns'}).inject(eItem, 'top'), //
      {
        baseUrl: this.options.baseUrl,
        onEditComplete: function(id) {
          this.reloadItem(id);
        }.bind(this),
        onDeleteComplete: function(id) {
          new Fx.Morph(eItem, {
            onComplete: function() {
              eItem.destroy();
            }
          }).start({
              height: 0,
              'margin-right': 0,
              opacity: 0
            });
        }.bind(this)
      }
    );
  },

  reloadItem: function(id) {
    window.location.reload();
  }

});
