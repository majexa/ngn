Ngn.cp.ddFieldType = {};
Ngn.cp.ddFieldType.types = {};

Ngn.cp.ddFieldType.Properties = new Class({

  initialize: function(eForm, name) {
    var init = function(type) {
      var changingTypes = ['required', 'defaultDisallow', 'system', 'notList', 'filterable'];
      for (var i = 0; i < changingTypes.length; i++) {
        var eRow = eForm.getElement('.row_' + changingTypes[i]);
        if (!eRow) continue;
        eRow.setStyle('display', 'block').getElements('input,select').set('disabled', false);
      }
      if (Ngn.cp.ddFieldType.types[type].disable) {
        var disable = Ngn.cp.ddFieldType.types[type].disable;
        for (i in disable) {
          var eRow = eForm.getElement('.row_' + disable[i]);
          if (!eRow) continue;
          eRow.setStyle('display', 'none').getElements('input,select').set('disabled', true);
        }
      }
    }
    Ngn.Frm.addEvent('change', name, init);
    init(Ngn.Frm.getValueByName(name));
    var selType = Ngn.Frm.getValueByName(name);
    if (Ngn.cp.ddFieldType.types[selType].virtual) init(selType);
  }

});