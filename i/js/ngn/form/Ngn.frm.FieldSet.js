Ngn.frm.fieldSets = [];

Ngn.frm.FieldSet = new Class({
  Extends: Ngn.FieldSet.Html,
  form: null, // Ngn.Form

  initialize: function(form, container, options) {
    this.form = form;
    Ngn.frm.fieldSets.include(this);
    this.parent(container, options);
    this.initVirtualElement(this.eContainer);
  },

  initInput: function(eInput) {
    this.form.initActiveEl(eInput);
  },

  afterAddRow: function(eNewRow) {
    this.form.addElements(eNewRow);
  }

});

Ngn.frm.FieldSet.implement(Ngn.frm.virtualElement);