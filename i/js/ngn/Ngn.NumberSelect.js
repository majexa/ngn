Ngn.NumberSelect = new Class({
  Implements: [Options, Events],

  options: {
    min: 1,
    max: 1000,
    step: 1
    //onChange: null
  },

  initialize: function(eInput, options) {
    this.setOptions(options);
    this.eInput = eInput;
    eInput.addEvent('blur', function() {
      eInput.set('value', parseInt(this.eInput.get('value')));
      this.fireEvent('change', parseInt(this.eInput.get('value')));
    }.bind(this));
    eInput.addEvent('keypress', function() {
      this.fireEvent('change', parseInt(this.eInput.get('value')));
    }.bind(this));
    new Element('input', {
      'class' : 'btnInput prev',
      type: 'button',
      value: '-'
    }).inject(eInput, 'before').
      addEvent('click', this.prev.bind(this));
    new Element('input', {
      'class' : 'btnInput next',
      type: 'button',
      value: '+'
    }).inject(eInput, 'after').
      addEvent('click', this.next.bind(this));
  },

  prev: function() {
    var curValue = parseInt(this.eInput.get('value')) - this.options.step < this.options.min ?
      this.options.min :
      parseInt(this.eInput.get('value')) - this.options.step;
    this.eInput.set('value', curValue);
    this.fireEvent('change', parseInt(this.eInput.get('value')));
  },

  next: function() {
    var curValue = parseInt(this.eInput.get('value')) + this.options.step > this.options.max ?
      this.options.max :
      parseInt(this.eInput.get('value')) + this.options.step;
    this.eInput.set('value', curValue);
    this.fireEvent('change', parseInt(this.eInput.get('value')));
  }

});
