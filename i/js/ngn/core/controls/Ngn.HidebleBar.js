Ngn.hidebleBarIds = [];
Ngn.HidebleBar = new Class({

  modes: ['up', 'down'],
  slideMode: 'vertical',

  initialize: function(eBar, mode) {
    this.mode = mode || this.modes[0];
    this.id = Ngn.hidebleBarIds.length + 1;
    Ngn.hidebleBarIds.push(this.id);
    this.eBar = document.id(eBar);
    this.initBarPosition = this.eBar.getPosition();
    this.eBar.addClass('hidebleBar ' + this.slideMode);
    this.eHandlerHide = new Element('div', {'class': 'hidebleBarHandler'}).addClass(this.slideMode).addClass('hide').addClass(this.mode);
    this.eHandlerShow = new Element('div', {'class': 'hidebleBarHandler'}).addClass(this.slideMode).addClass('show');
    var handleShowExtraClass = this.eBar.get('class').replace(/\s*dropRightMenu\s*/, '') || false;
    if (handleShowExtraClass) this.eHandlerShow.addClass(handleShowExtraClass);
    Ngn.HidebleBar.addHover(this.eHandlerHide, 'hover');
    Ngn.HidebleBar.addHover(this.eHandlerShow, 'hover');
    this.eHandlerHide.inject(this.eBar);
    this.eHandlerShow.inject(document.getElement('body'));
    this.positionHandlerShow();
    this.init();
    window.addEvent('resize', this.position.bind(this));
    var fxHide = new Fx.Slide(this.eBar, {
      mode: this.slideMode,
      duration: 100,
      onComplete: function() {
        this.hide();
        Ngn.Storage.set('hidebleBar' + this.id, false);
      }.bind(this)
    });
    var state = Ngn.Storage.bget('hidebleBar' + this.id);
    if (!state) {
      (function() {
        fxHide.hide();
        this.hide();
      }).delay(1, this);
    } else {
      this.eHandlerShow.setStyle('visibility', 'hidden');
    }
    var fxShow = new Fx.Slide(this.eBar, {
      mode: this.slideMode,
      duration: 100,
      onComplete: function() {
        window.fireEvent('resize');
        Ngn.Storage.set('hidebleBar' + this.id, true);
        this.eHandlerShow.setStyle('visibility', 'hidden');
      }.bind(this)
    });
    this.eHandlerHide.addEvent('click', function() {
      fxHide.slideOut();
    });
    this.eHandlerShow.addEvent('click', function() {
      fxShow.slideIn();
    }.bind(this));
  },

  hide: function() {
    this.eHandlerShow.setStyle('visibility', 'visible');
    window.fireEvent('resize');
  },

  position: function() {
    this.positionHandlerShow();
  },

  styleProp: 'top',
  positionProp: 'y',

  positionHandlerShow: function() {
    if (this.mode == this.modes[1]) {
      this.eHandlerShow.setStyle(this.styleProp, window.getSize()[this.positionProp] - this.eHandlerShow.getSize()[this.positionProp]);
    } else {
      this.eHandlerShow.setStyle(this.styleProp, this.initBarPosition[this.positionProp] + 'px');
    }
  },

  init: function() {
    this.eHandlerShow.addClass(this.mode == this.modes[1] ? this.modes[0] : this.modes[1]);
    if (this.mode == this.modes[0]) this.eHandlerHide.setStyle(this.styleProp, this.eBar.getSize()[this.positionProp] - this.eHandlerHide.getSize()[this.positionProp]);
  }

});


Ngn.HidebleBar.H = new Class({
  Extends: Ngn.HidebleBar,

  init: function() {
    this.parent();
    Ngn.setToCenterHor(this.eHandlerHide, this.eBar);
    Ngn.setToCenterHor(this.eHandlerShow, this.eBar);
  },

  position: function() {
    this.parent();
    Ngn.setToCenterHor(this.eHandlerHide);
    Ngn.setToCenterHor(this.eHandlerShow);
  }

});

Ngn.HidebleBar.V = new Class({
  Extends: Ngn.HidebleBar,

  modes: ['left', 'right'],
  slideMode: 'horizontal',
  styleProp: 'left',
  positionProp: 'x'

});

Ngn.HidebleBar.addHover = function(el, hoverClass) {
  el.addEvent('mouseover', function() {
    this.addClass(hoverClass);
  });
  el.addEvent('mouseout', function() {
    this.removeClass(hoverClass);
  });
};
