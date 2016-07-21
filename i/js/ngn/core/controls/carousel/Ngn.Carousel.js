// @requiresBefore i/js/ngn/core/Ngn.elementExtras.js
Ngn.Carousel = new Class({
  Extends: Fx.Scroll,

  options: {
    mode: 'horizontal',
    id: 'carousel',
    changeContainerWidth: true,
    childSelector: false,
    loopOnScrollEnd: true,
    periodical: false,
    btnPrevious: null,
    btnNext: null
  },

  initialize: function(element, options) {
    this.parent(element, options);
    this.cacheElements();
    this.element = this.element.getParent();
    for (var i = 0; i < this.elements.length; i++) {
      this.elements[i].store('initIndex', i);
    }
    this.currentIndex = 0;
    //this.elementWidth = this.elements[0].getSize().x;
    //this.visibleElementsN = Math.round(this.element.getSize().x / this.elementWidth);
    if (this.options.periodical) this.toNext.periodical(this.options.periodical, this);
    if (this.elements) this.toElementForce(this.elements[this.currentIndex]);
    //console.debug([this.element, this.element.getSize().x, this.wrapper.getSize().x]);
    if (this.original.getSize().x < this.wrapper.getSize().x) {
      if (this.options.btnPrevious) {
        this.options.btnPrevious.setStyle('display', 'none');
      }
      if (this.options.btnNext) {
        this.options.btnNext.setStyle('display', 'none');
      }
    } else {
      if (this.options.btnPrevious) {
        this.options.btnPrevious.addEvent('click', function() {
          this.toPrevious();
          return false;
        }.bind(this));
      }
      if (this.options.btnNext) {
        this.options.btnNext.addEvent('click', function() {
          this.toNext();
          return false;
        }.bind(this));
      }
    }
  },

  cacheElements: function() {
    var els;
    if (this.options.childSelector) {
      els = this.element.getElements(this.options.childSelector);
      //} else if (this.options.mode == 'horizontal'){
      //  els = this.element.getElements(':first-child > *');
    } else {
      els = this.element.getChildren();
    }
    if (!els[0]) throw new Error('No elements was found');
    this.wrapper = new Element('div', {
      'class': (this.element.get('class') || this.element.get('id')) + 'Wrapper',
      styles: {
        'overflow': 'hidden'
      }
    }).wraps(this.element);
    var w = 0;
    for (var i = 0; i < els.length; i++) {
      //if (!els[i].getSizeWithMargin()) console.debug(els[i]);
      w += els[i].getSizeWithMargin().x;
    }
    //w += eWrapper.getSize().x;
    this.element.setStyle('width', w + 'px');
    this.elements = els;
    this.original = this.element;
    return this;
  },

  curEl: null,

  setSelectedElement: function(el) {
    if (this.curEl) this.curEl.removeClass('sel');
    this.curEl = el.addClass('sel');
  },

  toNext: function() {
    if (!this.check()) return this;
    this.currentIndex = this.getNextIndex();
    if (!this.elements[this.currentIndex]) return;
    this.toElement(this.elements[this.currentIndex]);
    this.fireEvent('next');
    return this;
  },

  toPrevious: function() {
    if (!this.check()) return this;
    this.currentIndex = this.getPreviousIndex();
    if (!this.elements[this.currentIndex]) return;
    this.toElement(this.elements[this.currentIndex]);
    this.fireEvent('previous');
    return this;
  },

  toElement: function(el) {
    this.parent(el);
    this.setSelectedElement(el);
    this.fireEvent('toElement');
  },

  toElementForce: function(el) {
    var axes = ['x', 'y'];
    var scroll = this.element.getScroll();
    var position = Object.map(document.id(el).getPosition(this.element), function(value, axis) {
      return axes.contains(axis) ? value + scroll[axis] : false;
    });
    this.set(this.calculateScroll(position.x, position.y));
    this.setSelectedElement(el);
  },

  setRight: function() {
    this.set(this.element.getScrollSize().x, 0);
  },

  getNextIndex: function() {
    this.currentIndex++;
    if (this.currentIndex == this.elements.length || this.checkScroll()) {
      this.fireEvent('loop');
      this.fireEvent('nextLoop');
      return 0;
    } else {
      return this.currentIndex;
    }
  },

  getPreviousIndex: function() {
    this.currentIndex--;
    var check = this.checkScroll();
    if (this.currentIndex < 0 || check) {
      this.fireEvent('loop');
      this.fireEvent('previousLoop');
      return (check) ? this.getOffsetIndex() : this.elements.length - 1;
    } else {
      return this.currentIndex;
    }
  },

  getOffsetIndex: function() {
    var visible = (this.options.mode == 'horizontal') ? this.element.getStyle('width').toInt() / this.elements[0].getStyle('width').toInt() : this.element.getStyle('height').toInt() / this.elements[0].getStyle('height').toInt();
    return this.currentIndex + 1 - visible;
  },

  checkLink: function() {
    return (this.timer && this.options.link == 'ignore');
  },

  checkScroll: function() {
    var scroll, total;
    if (!this.options.loopOnScrollEnd) return false;
    if (this.options.mode == 'horizontal') {
      scroll = this.element.getScroll().x;
      total = this.element.getScrollSize().x - this.element.getSize().x;
    } else {
      scroll = this.element.getScroll().y;
      total = this.element.getScrollSize().y - this.element.getSize().y;
    }
    return (scroll == total);
  },

  getCurrent: function() {
    return this.elements[this.currentIndex];
  }

});
