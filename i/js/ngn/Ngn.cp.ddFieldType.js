Ngn.cp.ddFieldType = {};
Ngn.cp.ddFieldType.types = {};

Ngn.cp.ddFieldType.Properties = new Class({
  
  initialize: function(eForm, name) {
    var eName = eForm.getElement('#namei');
    var copyValue = function() {
      eForm.getElement('#titlei').set('value', eName.get('value'));
    };
    var init = function(type) {
      var changingTypes = ['required', 'defaultDisallow', 'system', 'notList', 'filterable'];
      for (var i=0; i<changingTypes.length; i++) {
        var eRow = eForm.getElement('.row_'+changingTypes[i]);
        if (!eRow) continue;
        eRow.setStyle('display', 'block');
        eRow.getElements('input,select').each(function(eInput){
          eInput.set('disabled', false);
        });
      }
      if (Ngn.cp.ddFieldType.types[type].disable) {
        var disable = Ngn.cp.ddFieldType.types[type].disable;
        for (i in disable) {
          var eRow = eForm.getElement('.row_'+disable[i]);
          if (eRow) {
            eRow.setStyle('display', 'none');
            eRow.getElements('input,select').each(function(eInput){
              eInput.set('disabled', true);
            });
          }
        }
      }
    }
    Ngn.frm.addEvent('change', name, function(type) {
      init(type);
    });
    init(Ngn.frm.getValueByName(name));
    var selType = Ngn.frm.getValueByName(name);
    if (Ngn.cp.ddFieldType.types[selType].virtual) init(selType);
  }
  
});