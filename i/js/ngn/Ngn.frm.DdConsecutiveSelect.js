Ngn.frm.DdConsecutiveSelect = new Class({
  Implements: [Events, Options],
  
  initialize: function(eParent, strName, options) {
    this.setOptions(options);
    this.eParent = eParent;
    this.eRequestValidator = this.eParent.getElement('.validate-request');
    this.strName = strName;
    this.init();
  },
  
  init: function() {
    this.eParent.getElements('select').each(function(el, i){
      if (el.retrieve('cs-initialized')) return;
      el.store('cs-initialized', true);
      el.addEvent('change', function(e){
        this.loadSelect(el);
      }.bind(this));
    }.bind(this));
  },
  
  loadSelect: function(eChangedSelect) {
    while (next = eChangedSelect.getNext()) next.dispose(); // убираем все элементы после
    if (!eChangedSelect.get('value')) return;
    var eRow = eChangedSelect.getParent('.element');
    eRow.addClass('hLoader');
    eChangedSelect.set('disabled', true);
    this.fireEvent('request', [eChangedSelect]);
    //this.eRequestValidator.set('value', '');
    new Request({
      url: '/c/ddTagsConsecutiveSelect/' + this.strName,
      onComplete: function(html) {
        //this.eRequestValidator.set('value', 'complete');
        eRow.removeClass('hLoader');
        eChangedSelect.set('disabled', false);
        if (!html) return;
        new Element('span', {html: html}).inject(eChangedSelect, 'after');
        this.fireEvent('complete');
        this.init();
      }.bind(this)
    }).GET({
      name: Ngn.frm.getPureName(eChangedSelect.get('name')),
      id: eChangedSelect.get('value')
    });
  }
  
});