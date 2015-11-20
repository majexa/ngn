Ngn.GroupToggler = new Class({
  Implements: [Options],

  options: {
    groupId: 'default',
    itemIdParam: function(eItem) {
      return eItem.get('class').replace(/.*name_(\w+).*/, '$1');
    },
    itemSelector: '.element',
    btnSelector: '.label',
    bodySelector: '.field-wrapper',
    //storage: true,
    openForever: false,
    displayValue: 'block'
  },

  initialize: function(eCont, options) {
    this.setOptions(options);
    eCont.getElements(this.options.itemSelector).each(function(eItem) {
      var eBtn = eItem.getElement(this.options.btnSelector);
      if (!eBtn) return;
      eBtn.addClass('pseudoLink').addClass('dgray');
      var eBody = eItem.getElement(this.options.bodySelector);
      if (this.options.openForever) {
        eBody.setStyle('display', 'none');
      } else {
        var id = this.options.itemIdParam(eItem);
        eBody.setStyle('display', this.display(id));
      }
      eBtn.addEvent('click', function(e) {
        e.preventDefault();
        if (this.options.openForever) {
          eBody.setStyle('display', this.options.displayValue);
          eBtn.dispose();
          eItem.getElements('.temp').each(function(el){
            el.dispose();
          })
        } else {
          eBody.setStyle('display', this.display(id, true));
          Ngn.Storage.set(this.id(id), !this.get(id));
        }
      }.bind(this));
    }.bind(this));
  },

  get: function(id) {
    return Ngn.Storage.get(this.id(id)) ? true : false;
  },

  display: function(id, invert) {
    return this.get(id) ? (invert ? 'none' : this.options.displayValue) : (invert ? this.options.displayValue : 'none');
  },

  id: function(id) {
    return this.options.groupId + id;
  }

});