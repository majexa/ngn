Ngn.cp.TwoPanels = new Class({
  Implements: [Options],
  
  options: {
    storeId: 'twoPanels',
    leftExcludeEls: [],
    rightExcludeEls: [],
    addLeftWrapper: true,
    addRightWrapper: true,
    dragOptions: {}
  },
  
  initialize: function(eLeft, eRight, eHandler, options) {
    this.setOptions(options);
    this.eLeft = eLeft;
    this.eRight = eRight;
    this.eHandler = eHandler;
    if (this.options.addLeftWrapper) this.eLeft = this.addWrapper(this.eLeft, 'panelWrapper');
    if (this.options.addRightWrapper) this.eRight = this.addWrapper(this.eRight, 'panelWrapper');
    this.options.dragOptions.onDrag = this.resize.bind(this);
    this.hHandler(this.eHandler, this.eLeft, this.options.storeId, this.options.dragOptions);
    this.handlerW = this.eHandler.getSize().x;
    // Элементы, высоты которых нужно вычитать не успевают отрендериться, поэтому ставим задержку
    (function() {
      this.init();
    }).delay(100, this);
  },

  hHandler: function(eHandler, eContainer, wId, dragOptions) {
    var w = Ngn.Storage.get(wId);
    dragOptions = dragOptions || {};
    if (w) eContainer.setStyle('width', w);
    new Drag(eContainer, Object.merge({
      handle: eHandler,
      modifiers: {x: 'width', y: false},
      snap: 0,
      onComplete: function(el) {
        Ngn.Storage.set(wId, el.getStyle('width'));
      }
    }, dragOptions));
  },

  addWrapper: function(el, wrapperClass) {
    var wrapper = new Element('div', {'class': wrapperClass}).inject(el, 'before');
    el.inject(wrapper);
    return wrapper;
  },
  
  leftMinusH: 0,
  //rightMinusH: 0,
  
  init: function() {
    window.addEvent('resize', this.resize.bind(this));
    this.resize();
  },

  getLeftMinusHeight: function() {
    var h = 0;
    for (i=0; i<this.options.leftExcludeEls.length; i++)
      h += this.options.leftExcludeEls[i].getSize().y;
    return h;
  },

  getRightMinusHeight: function() {
    var h = 0;
    for (i=0; i<this.options.rightExcludeEls.length; i++)
      h += this.options.rightExcludeEls[i].getSize().y;
    return h;
  },
  
  resize: function() {
    if (this.resizeTid) clearTimeout(this.resizeTid);
    this.resizeTid = this._resize.delay(10, this);
  },

  _resize: function() {
    this.eRight.setStyle('width', (window.getSize().x - (this.eHandler.getPosition().x + this.handlerW)) + 'px');
    var maH = Ngn.cp.getMainAreaHeight();
    this.eLeft.setStyle('height', (maH - this.getLeftMinusHeight()) + 'px');
    this.eRight.setStyle('height', (maH - this.getRightMinusHeight()) + 'px');
    this.eHandler.setStyle('height', maH + 'px');
  }
  
});

