Ngn.GroupToggler = new Class({
  Implements: [Options],

  options: {
    groupName: 'default',
    itemNameParam: function(eItem) {
      return eItem.get('class').replace(/.*name_(\w+).*/, '$1');
    },
    itemSelector: '.element',
    btnSelector: '.label',
    bodySelector: '.field-wrapper'
  },

  //togglers: {},

  initialize: function(eCont, options) {
    this.setOptions(options);
    eCont.getElements(this.options.itemSelector).each(function(eItem) {
      var eBtn = eItem.getElement(this.options.btnSelector);
      eBtn.addClass('pseudoLink').addClass('dgray');
      var eBody = eItem.getElement(this.options.bodySelector);
      var name = this.options.itemNameParam(eItem);
      /*
      this.togglers[name] = {
        eBtn: eBtn,
        eBtn: eBody
      };
      */
      eBody.setStyle('display', this.display(name));
      eBtn.addEvent('click', function(e) {
        e.preventDefault();
        eBody.setStyle('display', this.display(name, true));
        Ngn.storage.set(this.name(name), !this.get(name));
      }.bind(this));
    }.bind(this));
  },

  /*
  toggle: function(name, flag) {
    if (!this.togglers[name]) return;
    this.togglers[name].setStyle('display', flag ? 'block' : 'none');
    Ngn.storage.set(this.name(name), flag);
  },
  */

  get: function(name) {
    return Ngn.storage.get(this.name(name)) ? true : false;
  },

  display: function(name, invert) {
    return this.get(name) ? (invert ? 'none' : 'block') : (invert ? 'block' : 'none');
  },

  name: function(name) {
    return this.options.groupName + name;
  }

});