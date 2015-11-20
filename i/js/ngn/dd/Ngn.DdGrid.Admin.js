// @requiresBefore Ngn.DdFilterPath

Ngn.DdGrid.Admin = new Class({
  Extends: Ngn.DdGrid,
  options: {
    resizeble: true,
    basePath: Ngn.Url.getPath(3),
    filterPath: new Ngn.DdFilterPath(4),
    listAction: 'editContent',
    // idParam: 'itemId',
    toolActions: {
      edit: function(row) {
        new Ngn.Dialog.RequestForm({
          url: Ngn.Url.getPath(4) + '?a=json_edit&itemId=' + row.id,
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
        return Ngn.Url.getPath(4) + '?a=edit&itemId=' + row.id;
      }
    }
  }
  //initPagination: function(data, fromAjax) {
  //  this.parent(data, fromAjax);
  //  this.ePagination.inject(document.getElement('.pagePath'), 'top').addClass('pNumsTop');
  //}
});

/**
 * @param strName
 * @param options
 * @returns Ngn.DdGrid.Admin
 */
Ngn.DdGrid.Admin.factory = function(strName, options) {
  var cls = eval('Ngn.DdGrid.Admin.' + Ngn.String.ucfirst(strName));
  return cls ? new cls(options) : new Ngn.DdGrid.Admin(options);
};
