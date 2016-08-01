if (!Ngn.Admin) Ngn.Admin = {};

Ngn.Url = {};

Ngn.Url.getPath = function(n) {
  if (n === 0) return './';
  var p = window.location.pathname.split('/');
  var s = '';
  if (!n) n = p.length - 1;
  for (var i = 1; i <= n; i++) {
    s += '/' + (p[i] ? p[i] : 0);
    if (n === i) break;
  }
  return s;
};

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