// @requires Ngn.TinySettings.Simple.Links
Ngn.Form.El.WisiwigSimpleLinks = new Class({
  Extends: Ngn.Form.El.WisiwigSimple,

  getTinySettingsClass: function() {
    return Ngn.TinySettings.Simple.Links;
  },

  getTinySettingsOptions: function() {
    if (this.form.options.dialog && this.form.options.dialog.status) {
      return {
        status: this.form.options.dialog.status
      };
    }
    return {};
  }

});
