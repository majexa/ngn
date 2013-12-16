Ngn.Form.El.DdTagsTreeMultiselectDialogable = new Class({
  Extends: Ngn.Form.El.DdTagsTreeMultiselect,
  Implements: Options,

  updateHiddens: function(values) {
    values.each(function(item) {
      new Element('input', {
        type: 'hidden',
        name: this.name + '[]',
        value: item
      }).inject(this.fieldWrapper);
    }.bind(this));
    this.eReq.set('value', values.length != 0 ? 1 : '');
  },

  dialog: function() {
    return Ngn.Dialog.DdTagsTreeMultiselectDialog;
  },

  options: {
    selectText: ''
  },

  init: function() {
    this.parent();
    this.name = Ngn.frm.getPureName(this.eRow.getElement('input').get('name'));
    this.eTree = this.eRow.getElement('.field-wrapper div');
    this.fieldWrapper = this.eRow.getElement('.field-wrapper');
    var eA = new Element('a', {
      href: '#',
      'class': 'popupText dgray pseudoLink',
      html: this.options.selectText
    }).inject(this.fieldWrapper);
    var dialog = this.dialog();
    eA.addEvent('click', function(e) {
      new Event(e).stop();
      new dialog(this);
    }.bind(this));
    this.eReq = new Element('input', {
      type: 'hidden',
      name: this.name
    }).inject(this.fieldWrapper);
    this.eReq = new Element('input', {
      type: 'hidden',
      'class': 'required',
      name: 'dummy'
    }).inject(this.fieldWrapper);
  }

});