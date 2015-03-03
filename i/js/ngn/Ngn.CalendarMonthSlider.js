// @requires s2/js/common/config?name=ruMonths
Ngn.CalendarMonthSlider = new Class({

  initialize: function(eHeader) {
    this.btnPrev = eHeader.getElement('.prev');
    this.btnNext = eHeader.getElement('.next');
    this.el = eHeader.getElement('.current');
    var nextMonthYear = function(month, year) {
      if (month == 12) return [1, parseInt(year)+1];
      else return [parseInt(month)+1, parseInt(year)];
    };
    var prevMonthYear = function(month, year) {
      if (month == 1) return [12, parseInt(year)-1];
      else return [parseInt(month)-1, parseInt(year)];
    };
    var fxNext = new Fx.Slide(this.el, {
      mode: 'horizontal',
      onComplete: function() {
        if (this.from[0] != 0) return;
        var nextTitle = this.element.get('href').replace(new RegExp('.*d\\.(\\d+);(\\d+).*'), function(s, month, year) {
          var next = nextMonthYear(month, year);
          return Ngn.config.ruMonths[next[0]] + ' ' + next[1];
        });
        var nextLink = this.element.get('href').replace(new RegExp('d\\.(\\d+);(\\d+)'), function(s, month, year) {
          var next = nextMonthYear(month, year);
          return 'd.' + next[0] + ';' + next[1];
        });
        console.debug(nextTitle);
        this.element.set('html', nextTitle);
        this.element.set('href', nextLink);
      }
    });
    var fxPrev = new Fx.Slide(this.el, {
      mode: 'horizontal',
      onComplete: function() {
        if (this.from[0] != 0) return;
        var prevTitle = this.element.get('href').replace(new RegExp('.*d\\.(\\d+);(\\d+).*'), function(s, month, year) {
          var prev = prevMonthYear(month, year);
          return Ngn.config.ruMonths[prev[0]] + ' ' + prev[1];
        });
        var prevLink = this.element.get('href').replace(new RegExp('d\\.(\\d+);(\\d+)'), function(s, month, year) {
          var prev = prevMonthYear(month, year);
          return 'd.' + prev[0] + ';' + prev[1];
        });
        this.element.set('html', prevTitle);
        this.element.set('href', prevLink);
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
  }

});
