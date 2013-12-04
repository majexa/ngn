Ngn.Grid.Resizeble = new Class({
  Implements: [Options, Events],

  initialize: function(grid, options) {
    this.setOptions(options);
    this.grid = grid;
    this.initWrappers();
    this.initHanderls();
  },

  initWrappers: function() {
    this.grid.eParent.getElements('tr').each(function(eTr, trN) {
      eTr.getChildren('td,th').each(function(el, n) {
        if (trN > 2) {
          c(el);
          return;
        }
        var html = el.get('html');
        el.set('html', '');
        new Element('div', {
          html: '<div class="cont">' + html + '</div>',
          'class': 'wr',
          styles: {
            width: this.getWrWidth(n) + 'px'
          }
        }).inject(el);
      }.bind(this));
    }.bind(this));
  },

  initHanderls: function() {
    this.grid.eParent.getElements('tr')[1].getChildren('th,td').each(function(el, n) {
      if (el.hasClass('tools')) return;
      var eHandler = new Element('div', {
        'class': 'handler'
      }).inject(el, 'top');
      new Ngn.Grid.Resizeble.Col(this.grid, n, eHandler);
    });
  },

  getWrWidth: function(n) {
    return Ngn.storage.get('gridColWidth' + this.grid.options.id + n) || 50;
  }

});
