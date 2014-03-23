Ngn.Form.El.DdTagsConsecutiveSelect = new Class({
  Extends: Ngn.Form.El.Dd,

  init: function() {
    Ngn.frm.ConsecutiveSelect.factory(Ngn.frm.DdConsecutiveSelect, this, {
      strName: this.strName
    });
  }
  
});