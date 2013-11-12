Ngn.DdGrid.Admin = new Class({
  Extends: Ngn.DdGrid,
  options: {
    basePath: Ngn.getPath(3),
    filterPath: new Ngn.DdFilterPath(3),
    toolActions: {
      edit: function(row) {
        new Ngn.Dialog.RequestForm({
          url: Ngn.getPath(4) + '?a=json_edit&itemId=' + row.id,
          reduceHeight: true,
          title: false,
          onOkClose: function() {
            this.reload(row.id);
          }.bind(this)
        });
      }
    },
    toolLinks: {
      edit: function(row) {
        return Ngn.getPath(4) + '?a=edit&itemId=' + row.id;
      }
    }
  }
});

/**
 * @param strName
 * @param options
 * @returns Ngn.DdGrid.Admin
 */
Ngn.DdGrid.Admin.factory = function(strName, options) {
  var clsP = eval('Ngn.DdGrid.Admin.' + ucfirst(strName));
  if (Ngn.projectKey && clsP) {
    var cls = eval('Ngn.DdGrid.Admin.' + ucfirst(strName) + '.' + ucfirst(Ngn.projectKey));
    if (cls) return new cls(options);
  }
  return clsP ? new clsP(options) : new Ngn.DdGrid.Admin(options);
};
