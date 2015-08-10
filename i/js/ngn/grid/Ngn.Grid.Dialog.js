Ngn.Grid.Dialog = new Class({
  Extends: Ngn.Dialog,

  options: {
    footer: false,
    jsonRequest: true,
    gridClass: Ngn.Grid,
    reduceHeight: true,
    gridOpts: {}
  },

  init: function () {
    this.parent();
    if (this.options.textBefore) new Element('div', {'class':'textBefore', html: this.options.textBefore}).inject(this.message);
    this.options.gridOpts.eParent = new Element('div', {'class':'grid'}).inject(this.message);
    if (this.options.textAfter) new Element('div', {'class':'textAfter', html: this.options.textAfter}).inject(this.message);
    this.initGrid();
  },

  initGrid: function() {
    new this.options.gridClass(this.options.gridOpts);
  }

});

Ngn.Grid.Dialog.Request = new Class({
  Extends: Ngn.Grid.Dialog,

  beforeInitRequest: function() {
    console.trace(this.options.gridOpts);
    this.grid = new this.options.gridClass(Object.merge(this.options.gridOpts, {
      eParent: this.message,
      fromDialog: true,
      disableInit: true,
      debug: true
    }));
    this.options.url = this.grid.getLink(true);
  },

  urlResponse: function (r) {
    this.parent(r);
    if (!r.head) throw new Ngn.EmptyError('r.head');
    this.grid.dataLoaded(r);
  }

});
