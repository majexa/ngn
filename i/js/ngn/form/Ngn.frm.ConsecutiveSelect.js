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
    while (next = eChangedSelect.getNext()) next.dispose(); // убираем все элементы после
    if (!eChangedSelect.get('value')) return;
    var eRow = eChangedSelect.getParent('.element');
    eRow.addClass('hLoader');
    eChangedSelect.set('disabled', true);
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
    }).GET({
        name: Ngn.Frm.getPureName(eChangedSelect.get('name')),
        id: eChangedSelect.get('value')
      });
  }

});

/**
 * @param Ngn.Form.El.Dd
 * @param Ngn.Frm.ConsecutiveSelect
 * @returns object of class Ngn.Frm.ConsecutiveSelect
 */
Ngn.Frm.ConsecutiveSelect.factory = function(formEl, cls) {
  return new cls(formEl.eRow, formEl.fieldName, formEl.strName, formEl.parentTagFieldName, {
    onRequest: function(eSelect) {
      formEl.form.validator.resetField(eSelect);
    }.bind(formEl),
    onComplete: function() {
      formEl.form.validator.rewatchFields();
    }.bind(formEl)
  });
};