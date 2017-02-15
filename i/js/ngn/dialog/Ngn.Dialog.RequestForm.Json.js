Ngn.Dialog.RequestForm.Json = new Class({
  Extends: Ngn.Dialog.RequestForm,
  options: {
    jsonRequest: true,
    title: false
  },
  initialize: function(options) {
    this.parent(options);
    this.toggle('ok', true);
  },
  urlResponse: function (data) {
    this.parent({
      form: dot.template(this.options.formTmpl)(data)
    });
    this.form.eForm.set('action', this.options.url);
    this.toggle('ok', true);
  },
  initFailedEvent: function() {
    this.form.addEvent('failed', function(r) {
      this.loading(false);
    }.bind(this));
  }
});