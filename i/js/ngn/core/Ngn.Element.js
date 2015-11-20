Ngn.Element = {};

Ngn.Element._whenElPresents = function(elGetter, action, maxAttempts) {
  var el;
  el = elGetter();
  find = function() {
    return el = elGetter();
  };
  if (find()) {
    action(el);
    return;
  }
  maxAttempts = maxAttempts || 10;
  var n = 1;
  var id = function() {
    n++;
    if (find()) {
      clearTimeout(id);
      action(el);
      return;
    }
    if (n == maxAttempts) {
      clearTimeout(id);
      throw new Error('Element not presents after ' + maxAttempts + ' attempts');
    }
  }.periodical(200);
};

Ngn.Element.whenElPresents = function(eParent, selector, action) {
  return Ngn.Element._whenElPresents(function() {
    return eParent.getElement(selector);
  }, action);
};

Ngn.Element.bindSizes = function(eFrom, eTo) {
  eFrom.addEvent('resize', function() {
    eTo.setSize(eFrom.getSize());
  });
};
