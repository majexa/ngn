Ngn.DdItemsAddForm = new Class({

  initialize: function(eForm) {
    var options = {
      ajaxSubmit: true,
      onComplete: function() {
        window.location = '/list';
      }
    };
    if (!Ngn.authorized) {
      new Ngn.AuthDdForm(eForm, options);
    } else {
      new Ngn.DdForm(eForm, options);
    }
  }

});
