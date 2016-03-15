Ngn.Form.El.DialogSelect = new Class({
  Extends: Ngn.Form.El,
  options: {
    selectTitle: 'Нажмите, чтобы сменить',
    selectClass: ''
  },
  baseName: 'defualt',
  getInitField: function() {
    return this.eRow.getElement('input') || this.eRow.getElement('select');
  },
  getSelectDialogEl: function() {
    return new Element('a', {
      'class': 'pseudoLink dgray' + (this.options.selectClass ? ' ' + this.options.selectClass : ''),
      html: this.options.selectTitle
    }).inject(this.eInitField, 'after');
  },
  makeHiddenField: function() {
    this.eInput = new Element('input', { type: 'hidden', name: this.eInitField.get('name') }).inject(this.eInitField, 'after');
  },
  init: function() {
    this.eInitField = this.getInitField();
    this.value = this.eInitField.get('value');
    console.debug(this.value);
    this.makeHiddenField();
    this.eSelectDialog = this.getSelectDialogEl();
    new Element('div', {'class': 'rightFading'}).inject(this.eSelectDialog);
    this.eInitField.dispose();
    this.initControlDefault();
    this.setValue(this.value);
  },
  setValue: function(value) {
    this.setVisibleValue(value);
    this._setValue(value);
  },
  setVisibleValue: function(value) {
    this.eSelectDialog.set('html', value || 'не определён');
  },
  _setValue: function(value) {
    if (!value) return;
    this.value = value;
    this.eInput.set('value', value);
  },
  initControl: function() {
    this.eSelectDialog.addEvent('click', function() {
      var cls = this.getDialogClass();
      if (!cls) throw new Error('class not found');
      new cls(Object.merge({
        value: this.value
      }, this.getDialogOptions()));
    }.bind(this));
  },
  initControlDefault: function() {
    this.initControl();
  },
  getDialogClass: function() {
    throw new Error('Create abstract method getDialogClass()');
  },
  getDialogOptions: function() {
    return {
      onChangeValue: function(value) {
        this.setValue(value);
        if (this.form && this.form.options.dialog) {
          this.form.options.dialog.fireEvent('change' + this.baseName.capitalize(), value);
        }
      }.bind(this)
    };
  }
});