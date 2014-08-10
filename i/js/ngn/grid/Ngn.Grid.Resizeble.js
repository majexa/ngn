Ngn.Grid.Resizeble = new Class({
  Implements: [Options, Events],

  firstResizebleColN: 1,
  defaultColWidth: 50,
  debug: false,

  /**
   * @param Ngn.Grid
   * @param options
   */
  initialize: function(grid, options) {
    this.setOptions(options);
    this.grid = grid;
    if (this.debug) this.addDubugCells();
    this.initWrappers();
    this.initHanderls();
  },

  getTrParsents: function() {
    return this.grid.eParent.getChildren('table').getChildren()[0];
  },

  getWrWidth: function(n) {
    var w = Ngn.storage.get('gridColWidth' + this.grid.options.id + n);
    if (w > 250) w = 250;
    return w || this.defaultColWidth;
  },

  getRows: function() {
    var rows = [];
    this.getTrParsents().each(function(el) {
      el.getChildren().each(function(eTr) {
        if (eTr.hasClass('debug')) return;
        rows.push(eTr);
      });
    });
    return rows;
  },

  initWrappers: function() {
    this.getRows().each(function(eTr) {
      eTr.getChildren().each(function(eTd, n) {
        if (n < this.firstResizebleColN) return;
        var html = eTd.get('html');
        eTd.set('html', '');
        if (this.debug) this.debugCells[n].set('html', this.getWrWidth(n));
        new Element('div', {
          html: '<div class="cont">' + html + '</div>',
          'class': 'wr',
          styles: {
            width: this.getWrWidth(n) + 'px'
          }
        }).inject(eTd);
      }.bind(this));
    }.bind(this));
  },

  initHanderls: function() {
    this.cols = this.getTrParsents()[0].getChildren()[0].getChildren('th');
    this.cols.each(function(eTh, n) {
      if (n < this.firstResizebleColN + 1) return;
      var eHandler = new Element('div', {
        'class': 'handler'
      }).inject(eTh, 'top');
      var col = new Ngn.Grid.Resizeble.Col(this, n, eHandler);
      if (n == this.cols.length - 1) this.resizebleLastCol = col;
    }.bind(this));
    this.resizeLastCol();
  },

  resizeLastCol: function() {
    if (this.resizebleLastCol) this.resizebleLastCol.resizeLast();
  },

  debugCells: [],

  addDubugCells: function() {
    var eTbody = this.getTrParsents()[1];
    var cols = eTbody.getChildren()[0].getChildren();
    var eTr = new Element('tr', {'class': 'debug'});
    eTr.inject(eTbody);
    for (var i = 0; i < cols.length; i++) {
      this.debugCells[i] = new Element('td').inject(eTr);
    }
  }

});
