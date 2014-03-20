Ngn.Dialog.Link = new Class({
  Extends: Ngn.Dialog.Msg,

  options: {
    width: 120,
    ok: false,
    linkTitle: 'Открыть',
    bindBuildMessageFunction: true
    //link: ''
  },

  buildMessage: function() {
    return Elements.from('<h2 style="text-align: center"><a href="' + this.options.link + '" target="_blank">' + this.options.linkTitle + '</a></h2>')[0];
  }

});