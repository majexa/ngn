if (!Ngn.Admin) Ngn.Admin = {};

Ngn.Admin.UsersGrid = new Class({
  Extends: Ngn.Grid,

  options: {
    isSorting: false,
    id: 'users',
    resizeble: true,
    basePath: Ngn.Url.getPath(2),
    search: true,
    menu: [{
      title: Ngn.Locale.get('Core.add'),
      cls: 'add',
      action: function(grid) {
        new Ngn.Dialog.RequestForm({
          url: grid.options.basePath + '?a=json_new',
          width: 300,
          id: 'user',
          onOkClose: function() {
            grid.reload();
          }.bind(this)
        });
      }
    }],
    toolActions: {
      edit: function(row) {
        new Ngn.Dialog.RequestForm({
          url: '/admin/users/json_edit?id=' + row.id,
          width: 300,
          id: 'user',
          onOkClose: function() {
            this.reload();
          }.bind(this)
        });
      }
    }
  }

});