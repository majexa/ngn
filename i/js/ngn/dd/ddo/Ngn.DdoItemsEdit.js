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
        }.bind(this)
      }
    );
  },

  reloadItem: function(id) {
    window.location.reload();
  }

});
