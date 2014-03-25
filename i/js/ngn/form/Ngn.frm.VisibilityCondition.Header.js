Ngn.frm.VisibilityCondition.Header = new Class({
  Extends: Ngn.frm.VisibilityCondition,
  initSectionSelector: function() {
    this.sectionSelector = '.hgrp_' + this.sectionName;
  }
});
