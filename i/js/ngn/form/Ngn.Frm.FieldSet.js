Ngn.Frm.fieldSets = [];

Ngn.Frm.FieldSet = new Class({
  Extends: Ngn.FieldSet.Html,
  form: null, // Ngn.Form

  initialize: function(form, container, options) {
    this.form = form;
    Ngn.Frm.fieldSets.include(this);
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

Ngn.Frm.FieldSet.implement(Ngn.Frm.virtualElement);