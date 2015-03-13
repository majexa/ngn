Ngn.DdoCutText = new Class({
  Implements: Options,

  options: {
    charsLimit: 200,
    showText: 'показать полностью...'
  },

  initialize: function(options) {
    this.setOptions(options);
    document.getElements('.ddItems .item').each(function(eItem) {
      var eText = eItem.getElement('.f_text');
      if (!eText) return;
      var html = eText.get('html');
      if (html.length < this.options.charsLimit) return;
      eText.set('html', '');
      new Element('span', {
        html: html.substr(0, this.options.charsLimit)
      }).inject(eText);
      new Element('span', {
        'class': 'temp',
        html: '...<br>'
      }).inject(eText);
      new Element('a', {
        'class': 'toggler',
        html: this.options.showText
      }).inject(eText);
      new Element('span', {
        'class': 'afterCut',
        html: html.substr(this.options.charsLimit, html.length)
      }).inject(eText);
    }.bind(this));
    new Ngn.GroupToggler(document.getElement('.ddItems'), {
      itemSelector: '.item',
      btnSelector: '.toggler',
      bodySelector: '.afterCut',
      itemIdParam: function(eItem) {
        return str + eItem.get('data-id');
      },
      storage: false,
      openForever: true,
      displayValue: 'inline'
    });
  }

});
