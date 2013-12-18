Ngn.Form.El.DateRange = new Class({
  Extends: Ngn.Form.El.DialogSelect,

  getDialogClass: function() {
    return Ngn.DateRangeDialog;
  },
  setVisibleValue: function(value) {
    if (!value) return;
    var v;
    var d = new Date();
    if (value.from[2] == value.to[2]) {
      if (value.to[2] == d.getFullYear()) {
        if (value.from[0] == value.to[0] && value.from[1] == value.to[1]) {
          v = value.to[0] + ' ' + Ngn.config.ruMonths2[value.to[1]];
        } else {
          v = value.from[0] + ' ' + Ngn.config.ruMonths2[value.from[1]] + ' — ' + value.to[0] + ' ' + Ngn.config.ruMonths2[value.to[1]];
        }
      } else {
        v = value.from[0] + ' ' + Ngn.config.ruMonths2[value.from[1]] + ' — ' + value.to[0] + ' ' + Ngn.config.ruMonths2[value.to[1]] + ' ' + value.to[2];
      }
    } else {
      v = value.from[0] + ' ' + Ngn.config.ruMonths2[value.from[1]] + ' ' + value.to[2] + ' — ' + value.to[0] + ' ' + Ngn.config.ruMonths2[value.to[1]] + ' ' + value.to[2];
    }
    this.parent(v);
  },
  _setValue: function(value) {
    if (!value) return;
    this.parent(Ngn.DdFilterPath.date.toStr(value));
  }
});