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
    if (!this.formEl.value) {
      var d = new Date();
      from = to = [d.getDate(), d.getMonth() + 1, d.getFullYear()];
    } else {
      var o = Ngn.DdFilterPath.date.toObj(this.formEl.value);
      from = o.from;
      to = o.to;
    }
    Ngn.frm.getElements('from', this.message).each(function(el, n) {
      el.set('value', from[n]);
    });
    Ngn.frm.getElements('to', this.message).each(function(el, n) {
      el.set('value', to[n]);
    });
    //new Element('a', { class: 'gray pseudoLink', html: 'Сегодня' }).inject(this.message);
    //new Element('a', { class: 'gray pseudoLink', html: 'Последняя неделя' }).inject(this.message);
    //Ngn.btn1('Сегодня', 'btn ok').inject(this.message).addEvent('click', function() {
    //});
  },

  getValue: function() {
    var value = {
      from: [],
      to: []
    };
    Ngn.frm.getElements('from', this.message).each(function(el, n) {
      value.from.push(el.get('value'));
    });
    Ngn.frm.getElements('to', this.message).each(function(el, n) {
      value.to.push(el.get('value'));
    });
    return value;
  }


});