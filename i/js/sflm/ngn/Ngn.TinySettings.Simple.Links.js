Ngn.TinySettings.Simple.Links = new Class({
  Extends: Ngn.TinySettings.Simple,
  Implements: Options,
  
  options: {
    status: null
  },
  
  initialize: function(options) {
    this.setOptions(options);
    var obj = this;
    if (obj.options.status) {
      this.setups.push(function(ed) {
        ed.onNodeChange.add(function(ed, cm, el) {
          if (el.tagName == 'A') {
            //obj.options.status.set('html', '<a href="' + el.href + " target='_blank'>" + el.href.replace(window.location.origin, '') + "</a>");
            obj.options.status.set('html', el.href.replace(window.location.origin, ''));
          } else {
            obj.options.status.set('html', '');
          }
        });
      });
    }
  },

  getSettings: function() {
    var s = this.parent();
    s.theme_advanced_buttons1 += ',link,unlink';
    s.valid_elements += ',a[href|target]';
    return s;
  }

});
