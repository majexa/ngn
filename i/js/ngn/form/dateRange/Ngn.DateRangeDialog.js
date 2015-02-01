Ngn.DateRangeDialog = new Class({
  Extends: Ngn.ElSelectDialog,
  options: {
    width: 270,
    bindBuildMessageFunction: true,
    title: 'Выберите дату',
    dialogClass: 'dialog dateRangeDialog'
  },
  buildMessage: function() {
    return Ngn.tpls.dateRange;
  },
  init: function() {
    var from, to;
    if (!this.options.value) {
      var d = new Date();
      from = to = [d.getDate(), d.getMonth() + 1, d.getFullYear()];
    } else {
      //throw new Error('can not use Ngn.DdFilterPath here');
      var o = Ngn.DdFilterPath.date.toObj(this.options.value);
      from = o.from;
      to = o.to;
    }
    Ngn.Frm.getElements('from', this.message).each(function(el, n) {
      el.set('value', from[n]);
    });
    Ngn.Frm.getElements('to', this.message).each(function(el, n) {
      el.set('value', to[n]);
    });
    //new Element('a', { class: 'gray pseudoLink', html: 'Сегодня' }).inject(this.message);
    //new Element('a', { class: 'gray pseudoLink', html: 'Последняя неделя' }).inject(this.message);
    //Ngn.Btn.btn1('Сегодня', 'btn ok').inject(this.message).addEvent('click', function() {
    //});
  },
  getValue: function() {
    var value = {
      from: [],
      to: []
    };
    Ngn.Frm.getElements('from', this.message).each(function(el, n) {
      value.from.push(el.get('value'));
    });
    Ngn.Frm.getElements('to', this.message).each(function(el, n) {
      value.to.push(el.get('value'));
    });
    return value;
  }
});