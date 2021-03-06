Ngn.TinySettings.Simple = new Class({
  Extends: Ngn.TinySettings,
  Implements: [Options],

  initialize: function(options) {
    this.setOptions(options);
  },

  getSettings: function() {
    return Object.merge(this.parent(), {
      // justifyleft, justifycenter, justifyright
      theme_advanced_buttons1: 'undo,redo,bold,italic,formatselect,bullist,numlist,sub,sup,blockquote,cleanup,fullscreen,justifycenter',
      theme_advanced_buttons2: '',
      plugins: 'safari,fullscreen',
      valid_elements: 'i,em,strong,b,strikethrough,li,ul,ol,blockquote,h2[class],h3[class],h4[class],br,p[class],sub,sup'
    });
  }

});