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
      form: this.options.formTmpl
    });
    for (var i in data) {
      var input = this.form.eForm.getElement('*[name='+i+']');
      if (!input) continue;
      input.set('value', data[i]);
    }
    this.form.eForm.set('action', this.options.url);
    this.toggle('ok', true);
  },
  initFailedEvent: function() {
    this.form.addEvent('failed', function(r) {
      this.loading(false);
    }.bind(this));
  }
});