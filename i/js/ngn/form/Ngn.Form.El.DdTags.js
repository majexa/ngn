Ngn.Form.El.DdTags = new Class({
  Extends: Ngn.Form.El.Dd,

  init: function() {
    var eInput = this.eRow.getElement('input');
    var tl = new Ngn.TextboxList(eInput, {
      unique: true,
      plugins: {
        autocomplete: {
          onlyFromValues: true,
          queryRemote: true,
          remote: {
            url: '/default/ddTagsAc?strName=' + this.strName + '&fieldName=' + eInput.get('name')
          }
        }
      }
    });
    if (Ngn.Form.El.DdTags.values[this.form.id]) {
      var v = Ngn.Form.El.DdTags.values[this.form.id][this.name];
      if (v) {
        for (var i = 0; i < v.length; i++) {
          if (!v[i][1]) continue;
          tl.add(v[i][1], v[i][0], v[i][1], null/*, {
            properties: {
              title: 'path / to / tagItem' // дополнительная подсказка для тэга
            }
          }*/);
        }
      }
    }
  }

});
Ngn.Form.El.DdTags.values = {};