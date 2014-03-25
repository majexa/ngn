Ngn.Frm.VisibilityCondition.Header = new Class({
  Extends: Ngn.Frm.VisibilityCondition,
  initSectionSelector: function() {
    this.sectionSelector = '.hgrp_' + this.sectionName;
  }
});
