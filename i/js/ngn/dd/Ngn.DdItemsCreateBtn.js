Ngn.DdItemsCreateBtn = new Class({
  Implements: [Options, Events],

  initialize: function(el, options) {
    this.setOptions(options);
    el.addEvent('click', function() {
      new Ngn.Dialog.Auth({
        onAuthComplete: function() {
          new Ngn.Dialog.RequestForm({
            url: '?a=new'
          });
        },
        reloadOnAuth: false
      });
    });
    Ngn.Form.elN
  }

});