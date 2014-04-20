Ngn.Dialog.VResize = new Class({

  initialize: function(dialog) {
    this.dialog = dialog;
    Ngn._whenElPresents(this.getResizebleEl.bind(this), this.init.bind(this));
  },

  init: function() {
    var eResizeble = this.getResizebleEl();
    this.eHandler = new Element('div', {'class': 'vResizeHandler'}).inject(this.dialog.eMessage);
    this.dialog.dialog.addClass('vResize');
    var storeK = this.dialog.options.id + '_height';
    var h = Ngn.storage.get(storeK);
    if (h) eResizeble.setStyle('height', h + 'px');
    new Drag(eResizeble, {
      preventDefault: true,
      stopPropagation: true,
      snap: 0,
      handle: this.eHandler,
      modifiers: {y: 'height', x: null},
      onComplete: function() {
        Ngn.storage.set(storeK, eResizeble.getSize().y);
      }
    });
    this.eHandler.inject(this.dialog.eMessage);
  },

  getResizebleEl: function() {
    return this.dialog.eMessage;
  }

});
