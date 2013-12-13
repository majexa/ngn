Ngn.Dialog.DdTagsTreeMultiselectDialog = new Class({
  Extends: Ngn.Dialog,

  options: {
    title: '',
    textInfo: '',
    height: 400,
    noPadding: false,
    cancel: false,
    bindBuildMessageFunction: true
  },

  initialize: function(formEl, opts) {
    this.formEl = formEl;
    this.parent(opts);
  },

  buildMessage: function() {
    this.form = new Element('p', {
      html: this.options.textInfo
    });
    this.formEl.eTree.inject(this.form);
    this.form.getElements('input').each(function(el) {
      el.addEvent('change', function() {
        this.formEl.updateHiddens(Ngn.frm.getValues(this.form.getElements('input')));
      }.bind(this));
    }.bind(this));
    return this.form;
  }

});

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
      'class': 'popupText black',
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
