Ngn.TinySettings.Simple = new Class({
  Extends: Ngn.TinySettings,

  getSettings: function() {
    c('ok');
    return $merge(this.parent(), {
      // justifyleft, justifycenter, justifyright
      theme_advanced_buttons1: 'undo,redo,bold,italic,formatselect,bullist,numlist,sub,sup,blockquote,cleanup,fullscreen,justifycenter',
      theme_advanced_buttons2: '',
      plugins: 'safari,fullscreen',
      valid_elements: 'i,em,strong,b,strikethrough,li,ul,ol,blockquote,h2[class],h3[class],h4[class],br,p[class],sub,sup'
    });
  }

});

Ngn.TinySettings.Simple.Links = new Class({
  Extends: Ngn.TinySettings.Simple,

  getSettings: function() {
    var s = this.parent();
    s.theme_advanced_buttons1 += ',link,unlink';
    s.valid_elements += ',a[href|target]';
    return s;
  }

});
