Ngn.SwitcherLink = new Class({

  Implements: [Options, Events],

  options: {
    textSelector: null,
    classOn: '',
    classOff: '',
    linkOn: '',
    linkOff: '',
    titleOn: 'Включить',
    titleOff: 'Выключить',
    onClick: Function.from(),
    onComplete: Function.from()
  },

  initialize: function(el, options) {
    this.setOptions(options);
    this.el = $(el) || el;
    if (this.options.textSelector) this.text = this.el.getElement(this.options.textSelector);
    if (this.el.hasClass(this.options.classOn)) {
      Ngn.Element.setTip(this.el, this.options.titleOff);
      if (this.text) this.text.set('text', this.options.titleOff);
    } else {
      Ngn.Element.setTip(this.el, this.options.titleOn);
      if (this.text) this.text.set('text', this.options.titleOn);
    }
    this.setTip(this.el.hasClass(this.options.classOn));
    this.el.addEvent('click', function(e) {
      e.preventDefault();
      this.click();
    }.bind(this));
  },

  click: function() {
    var enabled = this.el.hasClass(this.options.classOn);
    this.fireEvent('click');
    new Request({
      url: enabled ? this.options.linkOff : this.options.linkOn,
      onComplete: function(data) {
        this.setTip(!enabled);
        this.fireEvent('complete', !enabled);
      }.bind(this)
    }).get();
  },

  setTip: function(enabled) {
    if (enabled) {
      this.el.removeClass(this.options.classOff);
      this.el.addClass(this.options.classOn);
      Ngn.Element.setTip(this.el, this.options.titleOff);
      if (this.text) this.text.set('text', this.options.titleOff);
    } else {
      this.el.removeClass(this.options.classOn);
      this.el.addClass(this.options.classOff);
      Ngn.Element.setTip(this.el, this.options.titleOn);
      if (this.text) this.text.set('text', this.options.titleOn);
    }
  }

});
