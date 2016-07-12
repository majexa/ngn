Ngn.DdoItemsEdit = new Class({
  Extends: Ngn.AuthEditItems,
  Implements: [Events],

  options: {
    items: '.ddItems .item'
  },

  initialize: function(options) {
    this.parent(options);
    //console.debug(this.esItems);
    //new Sortables(this.esItems, {
    //  handle: '.grag',
    //  clone: false
    //});
  },

  //esItems: [],

  initItem: function(eItem) {
    //this.esItems.push(eItem);
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
        }.bind(this),
        movable: true
      }
    );
  },

  reloadItem: function(id) {
    window.location.reload();
  }

});
