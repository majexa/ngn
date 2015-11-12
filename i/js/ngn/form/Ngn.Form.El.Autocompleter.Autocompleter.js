Ngn.Form.El.Autocompleter.Autocompleter = new Class({
  Extends: Ngn.Autocompleter.Request.JSON,

  options: {
    caption: 'Нажмите клавишу "вниз" для выбора вариантов',
    postVar: 'mask',
    minLength: 1,
    //onFocus: function(el) {
    //el.set('value', '');
    //},
    //onBlur: function(el) {
    //el.set('value', this.selectedValue);
    //},
    onSelection: function(el, d) {
      this.selectedValue = this.element.value;
      var eInputValue = el.getParent('.element').getElement('input.val');
      eInputValue.set('value', d.inputKey);
    },
    onRequest: function() {
      this.eSpan2.addClass('loader');
    },
    onComplete: function() {
      this.eSpan2.removeClass('loader');
    }
  },

  initialize: function(eInput, options) {
    this.selectedValue = '';
    var type = eInput.getParent('.element').get('class').replace(/.*type_(\w+).*/, '$1');
    var controller = '/' + Ngn.sflmFrontend + '/ac' + Ngn.String.ucfirst(type);
    this.parent(eInput, controller, options);
    this.eSpan = new Element('span', {'class': 'ac'});
    this.eSpan2 = new Element('span', {'class': 'ac2'});
    this.eSpan.inject(this.element.getParent());
    this.eSpan2.inject(this.eSpan);
    this.element.inject(this.eSpan2);
    this.element.addClass('ac');
    this.eSpan.set('title', this.options.caption);
    this.eSpan.addClass('tooltip');
    this.element.inject(new Element('a', {'class': 'ac'}), 'after');
  },

  prefetch: function() {
    if (this.element.value && this.element.value == this.selectedValue) return;
    this.parent();
  }

});
