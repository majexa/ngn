Ngn.Form.El.DdTagsConsecutiveSelect = new Class({
  Extends: Ngn.Form.El.Dd,

  init: function() {
    Ngn.Frm.ConsecutiveSelect.factory(Ngn.Frm.DdConsecutiveSelect, this, {
      strName: this.strName
    });
  }
  
});