Ngn.Grid.Resizeble.Col = new Class({

  initialize: function(resizeble, colN, eHandler) {
    this.resizeble = resizeble;
    if (!this.resizeble.grid.options.id) throw new Error('cat use resizeble on grid without id option');
    this.colN = colN;
    this.drag = new Drag(new Element('div'), {
      handle: eHandler,
      onStart: function(el, e) {
        this.startPosition = e.page.x;
        this.startW = this.getElements(1)[0].getSize().x;
      }.bind(this),
      onDrag: function(el, e) {
        var delta = this.startPosition - e.page.x;
        var els = this.getElements(1);
        for (var i = 0; i < els.length; i++) {
          var w = this.startW - delta;
          if (!els[i]) throw new Error('why?');
          els[i].setStyle('width', w + 'px');
          if (this.resizeble.debug) this.resizeble.debugCells[this.colN - 1].set('html', w);
        }
        this.resizeble.resizeLastCol();
      }.bind(this),
      onComplete: function() {
        Ngn.Storage.set('gridColWidth' + this.resizeble.grid.options.id + (this.colN - 1), parseInt(this.getElements(1)[0].getStyle('width')));
      }.bind(this),
      snap: 0
    });
  },

  getMaxParentWidth: function() {
    var x1 = window.getSize().x;
    var x2 = this.resizeble.grid.eParent.getSize().x;
    return x1 < x2 ? x1 : x2;
  },

  resizeLast: function() {
    if (this.colN != this.resizeble.cols.length - 1) throw new Error('U can not use this method on non last col (' + this.colN + '). Total: ' + this.resizeble.cols.length);
    var els = this.getElements();
    var display = els[0].getStyle('display');
    this.setColCellsStyle('display', 'none');
    var parentWithoutLastColW = this.getMaxParentWidth();
    var tableW = this.resizeble.grid.eParent.getElement('table').getSize().x;
    this.setColCellsStyle('display', display);
    this.setColCellsStyle('width', parentWithoutLastColW - tableW);
  },

  setColCellsStyle: function(k, v) {
    var els = this.getElements();
    for (var i = 0; i < els.length; i++) els[i].setStyle(k, v);
  },

  getElements: function(offset) {
    var els = [];
    offset = offset || 0;
    this.resizeble.getRows().each(function(eTr) {
      eTr.getChildren('td,th').each(function(el, n) {
        if (n == this.colN - offset) {
          els.push(el.getElement('.wr'));
        }
      }.bind(this));
    }.bind(this));
    return els;
  }

});
