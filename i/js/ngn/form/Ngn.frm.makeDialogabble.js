Ngn.Frm.makeDialogabble = function(eLink, action, options) {
  eLink.addEvent('click', function(e) {
    e.preventDefault();
    new Ngn.Dialog.RequestForm(Object.merge({
      url: eLink.get('href').replace(action, 'json_' + action),
      onSubmitSuccess: function() {
        window.location.reload();
      }
    }, options || {}));
  });
};

