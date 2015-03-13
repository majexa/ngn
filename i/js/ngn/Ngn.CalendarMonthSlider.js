// @requires s2/js/common/config?name=ruMonths
Ngn.CalendarMonthSlider = new Class({

  initialize: function(eHeader) {
    this.currentMonth = eHeader.get('data-m');
    this.currentYear = eHeader.get('data-y');
    this.btnPrev = eHeader.getElement('.prev');
    this.btnNext = eHeader.getElement('.next');
    this.el = eHeader.getElement('.current');
    var o = this;
    var fxNext = new Fx.Slide(this.el, {
      mode: 'horizontal',
      onComplete: function() {
        if (this.from[0] != 0) return;
        o.changeLinkElement(o.nextMonthYear(), this.element);
      }
    });
    var fxPrev = new Fx.Slide(this.el, {
      mode: 'horizontal',
      onComplete: function() {
        if (this.from[0] != 0) return;
        o.changeLinkElement(o.prevMonthYear(), this.element);
      }
    });
    this.btnNext.addEvent('click', function(e) {
      e.preventDefault();
      fxNext.slideOut().chain(fxNext.slideIn);
    });
    this.btnPrev.addEvent('click', function(e) {
      e.preventDefault();
      fxPrev.slideOut().chain(fxPrev.slideIn);
    });
  },

  nextMonthYear: function() {
    if (this.currentMonth == 12) return [1, parseInt(this.currentYear)+1];
    else return [parseInt(this.currentMonth)+1, parseInt(this.currentYear)];
  },

  prevMonthYear: function() {
    if (this.currentMonth == 1) return [12, parseInt(this.currentYear)-1];
    else return [parseInt(this.currentMonth)-1, parseInt(this.currentYear)];
  },

  changeLinkElement: function(data, el) {
    this.currentMonth = data[0];
    this.currentYear = data[1];
    el.set('html', Ngn.config.ruMonths[data[0]] + ' ' + data[1]);
    el.set('href', 'd.' + data[0] + ';' + data[1]);
  }

});