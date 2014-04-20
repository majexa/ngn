/**
 * @requires Ngn.TinySettings.Simple
 */
Ngn.Form.El.WisiwigSimple = new Class({
  Extends: Ngn.Form.El.Wisiwig,

  init: function() {
    //if (this.name != 'descr') return;
    this.parent();
    var settings = new (this.getTinySettingsClass())().getSettings();
    if (this.options.tinySettings) settings = $merge(settings, this.options.tinySettings);
    new Ngn.TinyInit({
      parent: this.form.eForm,
      selector: '.name_' + this.name + ' textarea',
      settings: settings
    });
  },

  getTinySettingsClass: function() {
    return Ngn.TinySettings.Simple;
  }

});