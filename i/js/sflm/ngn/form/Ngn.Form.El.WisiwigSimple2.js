/**
 * @requires Ngn.TinySettings.Simple.Links
 */
Ngn.Form.El.WisiwigSimple2 = new Class({
  Extends: Ngn.Form.El.WisiwigSimple,

  getTinySettingsClass: function() {
    return Ngn.TinySettings.Simple.Links;
  }

});
