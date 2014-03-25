Ngn.Frm.VisibilityCondition.Field = new Class({
  Extends: Ngn.Frm.VisibilityCondition,
  initSectionSelector: function() {
    this.sectionSelector = '.name_' + this.sectionName;
  }
});
