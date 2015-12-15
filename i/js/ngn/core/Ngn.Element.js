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

Ngn.Element.initTips = function(els) {
  if (!Ngn.tips) Ngn.Element.tips = new Tips(els);
};

Ngn.Element.setTip = function(el, title) {
  if (!Ngn.Element.tips) Ngn.Element.initTips(el);
  if (el.retrieve('tip:native')) {
    Ngn.Element.tips.hide(el);
    this.store('tip:title', title);
  } else {
    Ngn.Element.tips.attach(el);
  }
};
