Ngn.Form.El.WisiwigSimple = new Class({
  Extends: Ngn.Form.El.Wisiwig,

  init: function() {
    this.parent();
    var settings = new (this.getTinySettingsClass())().getSettings();
    if (this.options.tinySettings) settings = $merge(settings, this.options.tinySettings);
    new Ngn.TinyInit({
      parent: this.form.eForm,
      selector: '.type_' + this.type + ' textarea',
      settings: settings
    });
  },

  getTinySettingsClass: function() {
    return Ngn.TinySettings.Simple;
  }

});