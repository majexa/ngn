Ngn.FramesSlider = new Class({
  Implements: [Options, Events],
  options: {
    frameCssClass: 'frame',
    transition: Fx.Transitions.Linear,
    duration: 300,
    frameWidth: null
  },
  oneTimeCompleteAction: false,
  frameN: 0,
  toggleDisplay: function() {
    for (var i = 1; i < this.frames.length; i++) {
      if (this.frameN == i) continue;
      this.frames[i].setStyle('display', 'none');
    }
  },
  /**
   * Нет необходимости выставлять стили для фреймов. Их определение заложено в клиентской бизнес-логике
   *
   * @param framesContainer Контейнер по ширине одного фрейма
   * @param options
   */
  initialize: function(framesContainer, options) {
    this.setOptions(options);
    this.frameWidth = this.options.frameWidth || window.getSize().x;
    this.eFramesContainer = framesContainer;
    this.eFramesContainer.setStyle('overflow', 'hidden');
    this.eFramesContainer.setStyle('width', this.frameWidth + 'px');
    this.frames = this.eFramesContainer.getElements('.' + this.options.frameCssClass);
    if (!this.frames.length) throw new Error('There are no frames in container by class "' + this.options.frameCssClass + '"');
    this.eFrames = new Element('div', {'class': 'frames'}).inject(this.eFramesContainer).adopt(this.frames);
    this.initFramesWidth();
    this.status = 0;
    this.eFramesContainer.setStyle('height', this.frames[this.frameN].getSize().y);
    this.fx = new Fx.Tween(this.eFrames, {
      property: 'margin-left',
      duration: this.options.duration,
      transition: this.options.transition,
      link: 'ignore',
      onStart: (function() {
        this.status = 1;
      }).bind(this),
      onComplete: (function() {
        this.status = 0;
        this.eFramesContainer.setStyle('height', this.frames[this.frameN].getSize().y);
        console.debug(this.getScrollOffset());
        window.scrollTo(0, this.getScrollOffset());
        if (this.oneTimeCompleteAction) {
          this.oneTimeCompleteAction();
          this.oneTimeCompleteAction = false;
        }
      }).bind(this)
    });
    window.addEvent('scroll', function(e) {
      //if (this.frameN == 2) return;
      this.scrollStorage[this.getScrollKey()] = document.getScroll().y;
    }.bind(this));
  },
  getScrollKey: function() {
    return this.frameN;
  },
  getScrollOffset: function() {
    return this.scrollStorage[this.getScrollKey()] || 0;
  },
  scrollStorage: {},
  initFramesWidth: function(offset) {
    this.eFrames.setStyle('width', this.framesWidth(offset) + 'px');
    for (var i = 0; i < this.frames.length; i++) {
      this.setFrameStyles(this.frames[i]);
    }
  },
  setFrameStyles: function(frame) {
    frame.setStyle('width', this.frameWidth + 'px');
    frame.setStyle('float', 'left');
  },
  framesWidth: function(offset) {
    if (!offset) offset = 0;
    return this.frameWidth * (this.frames.length + offset);
  }, // move logic
  getCurrPos: function() {
    return parseInt(this.eFrames.getStyle('margin-left'));
  },
  next: function(onComplete) {
    if (this.status) return;
    this.frameN++;
    var hasNext = !(this.frames.length - 1 == this.frameN);
    if (onComplete) this.oneTimeCompleteAction = onComplete.pass(hasNext);
    this.fx.start(this.getCurrPos(), this.getCurrPos() - this.frameWidth);
  },
  prev: function(onComplete) {
    if (this.status) return;
    this.oneTimeCompleteAction = onComplete;
    this.frameN--;
    this.fx.start(this.getCurrPos(), this.getCurrPos() + this.frameWidth);
  }, // frames manager
  pushFrame: function(html) {
    this.initFramesWidth(1);
    this.frames.push(new Element('div', {
      'class': this.options.frameCssClass,
      html: html
    }).inject(this.eFrames));
    this.setFrameStyles(this.frames[this.frames.length - 1]);
  },
  popFrame: function() {
    this.frames[this.frames.length - 1].dispose();
    this.frames.pop();
    this.initFramesWidth();
  }

});
