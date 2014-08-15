Ngn.Frm.ConsecutiveSelect = new Class({
  Implements: [Events, Options],

  initialize: function(eParent, options) {
    this.setOptions(options);
    this.eParent = eParent;
    this.init();
  },

  init: function() {
    var el = this.eParent.getElement('select');
    if (el.retrieve('cs-initialized')) return;
    el.store('cs-initialized', true);
    el.addEvent('change', function(e) {
      this.loadSelect(el);
    }.bind(this));
    /*
     this.eParent.getElements('select').each(function(el, i) {
     if (el.retrieve('cs-initialized')) return;
     el.store('cs-initialized', true);
     el.addEvent('change', function(e) {
     this.loadSelect(el);
     }.bind(this));
     }.bind(this));
     */
  },

  url: function() {
    throw new Error('Method is abstract');
  },

  loadSelect: function(eChangedSelect) {
    var next;
    while (next = eChangedSelect.getNext()) next.dispose(); // убираем все элементы после
    if (!eChangedSelect.get('value')) return;
    var eRow = eChangedSelect.getParent('.element');
    eRow.addClass('hLoader');
    eChangedSelect.set('disabled', true);
    return;
    this.fireEvent('request', [eChangedSelect]);
    new Request({
      url: this.url(),
      onComplete: function(html) {
        eRow.removeClass('hLoader');
        eChangedSelect.set('disabled', false);
        if (!html) return;
        new Element('span', {html: html}).inject(eChangedSelect, 'after');
        this.fireEvent('complete');
        this.init();
      }.bind(this)
    }).get({
        name: Ngn.Frm.getPureName(eChangedSelect.get('name')),
        id: eChangedSelect.get('value')
      });
  }

});

/**
 * @param cls Ngn.Frm.ConsecutiveSelect class
 * @param el Ngn.Form.El.Dd
 * @param opt
 * @returns Ngn.Frm.ConsecutiveSelect object
 */
Ngn.Frm.ConsecutiveSelect.factory = function(cls, el, opt) {
  opt = Object.merge({
    onRequest: function(eSelect) {
      this.form.validator.resetField(eSelect);
    }.bind(el),
    onComplete: function() {
      this.form.validator.rewatchFields();
    }.bind(el)
  }, opt);
  return new cls(el.eRow, opt);
};
