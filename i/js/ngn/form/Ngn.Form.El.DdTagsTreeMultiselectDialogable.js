Ngn.Form.El.DdTagsTreeMultiselectDialogable = new Class({
  Extends: Ngn.Form.El.DdTagsTreeMultiselect,
  Implements: Options,

  initUpdate: function(eContainer) {
    eContainer.getElements('input').each(function(el) {
      el.addEvent('change', function() {
        this.update();
      }.bind(this));
    }.bind(this));
  },

  branchLoaded: function(eParent) {
    this.initUpdate(eParent);
  },

  update: function() {
    this._update(Ngn.Frm.getValues(this.eParent.getElements('ul input')));
  },

  _update: function(values) {
    this.updateTitles(values);
    this.updateHiddens(values);
  },

  updateTitles: function(values) {
    if (!values.length) {
      this.eSelect.set('html', this.options.selectText);
      return;
    }
    var titles = [];
    for (var i = 0; i < values.length; i++) {
      titles.push(this.eParent.getElement('#' + this.name + '_' + values[i]).getNext().get('text'));
    }
    this.eSelect.set('html', Ngn.cut(titles.join(', '), 50));
  },

  updateHiddens: function(values) {
    this.eHiddens.set('html', '');
    values.each(function(item) {
      new Element('input', {
        type: 'hidden',
        name: this.name + '[]',
        value: item
      }).inject(this.eHiddens);
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
    this.name = Ngn.Frm.getPureName(this.eRow.getElement('input').get('name'));
    this.eTree = this.eRow.getElement('.field-wrapper div');
    this.eFieldWrapper = this.eRow.getElement('.field-wrapper');
    this.eHiddens = new Element('div.hiddens').inject(this.eFieldWrapper);
    this.eSelect = new Element('a', {
      href: '#',
      'class': 'popupText dgray pseudoLink',
      html: this.options.selectText
    }).inject(this.eFieldWrapper);
    var dialog = this.dialog();
    this.eSelect.addEvent('click', function(e) {
      new Event(e).stop();
      new dialog(this);
    }.bind(this));
    this.eReq = new Element('input', {
      type: 'hidden',
      'class': 'required',
      name: 'dummy'
    }).inject(this.eFieldWrapper);
    this.update();
  }

});