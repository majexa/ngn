Ngn.Btn.Dropdowner = new Class({
  Extends: Ngn.Btn,

  initialize: function(iconBtns) {
    this.iconBtns = iconBtns;
    this.eWrapper = new Element('div', {
      styles: {
        'border': '1px solid #FF0000'
      }
    }).inject(this.iconBtns[0], 'before');
    for (var i = 0; i < this.iconBtns.length; i++) this.iconBtns[i].inject(this.eWrapper);
  },

  rebuild: function() {
  }

});