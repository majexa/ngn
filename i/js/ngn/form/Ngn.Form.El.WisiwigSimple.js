/**
 * @requires Ngn.TinySettings.Simple
 */
Ngn.Form.El.WisiwigSimple = new Class({
  Extends: Ngn.Form.El.Wisiwig,

  options: {
    tinySettings: null
  },

  init: function() {
    this.parent();
    var settings = new (this.getTinySettingsClass())(this.getTinySettingsOptions()).getSettings();
    if (this.options.tinySettings) settings = $merge(settings, this.options.tinySettings);
    new Ngn.TinyInit({
      parent: this.form.eForm,
      selector: '.name_' + this.name + ' textarea',
      settings: settings
    });
  },

  getTinySettingsClass: function() {
    return Ngn.TinySettings.Simple;
  },

  getTinySettingsOptions: function() {
    return {};
  }

});