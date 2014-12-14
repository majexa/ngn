window.addEvent('domready', function() {
  document.getElements('.apeform form').each(function(eForm) {
    Ngn.Form.factory(eForm);
  });
});
