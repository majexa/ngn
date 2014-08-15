/*
Ngn.Frm.virtualElement = {
  // abstract toggleDisabled: function(flag) {},
  parentForm: null,
  initVirtualElement: function(el) {
    var eForm = el.getParent('form');
    if (!eForm) return;
    eForm.storeAppend('virtualElements', this);
  },
  getForm: function() {
  }
};
*/