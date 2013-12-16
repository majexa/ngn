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