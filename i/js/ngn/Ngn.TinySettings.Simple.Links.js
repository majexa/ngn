Ngn.TinySettings.Simple.Links = new Class({
  Extends: Ngn.TinySettings.Simple,

  getSettings: function() {
    var s = this.parent();
    s.theme_advanced_buttons1 += ',link,unlink';
    s.valid_elements += ',a[href|target]';
    return s;
  }

});
