Ngn.frm.VisibilityCondition.Field = new Class({
  Extends: Ngn.frm.VisibilityCondition,
  initSectionSelector: function() {
    this.sectionSelector = '.name_' + this.sectionName;
  }
});
