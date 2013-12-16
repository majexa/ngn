<div id="table"></div>
<script>
new Ngn.Grid({
  isSorting: false,
  id: 'users',
  resizeble: true,
  menu: [
    {
      title: 'Добавить пользователя',
      cls: 'add',
      action: function(grid) {
        new Ngn.Dialog.RequestForm({
          url: grid.options.basePath + '?a=json_new',
          onOkClose: function() {
            grid.reload();
          }.bind(this)
        });
      }
    }
  ],
  toolActions: {
    edit: function(row) {
      new Ngn.Dialog.RequestForm({
        url: '/admin/users/json_edit?id='+row.id,
        onOkClose: function() {
          this.reload();
        }.bind(this)
      });
    }
  },
  data: <?= Arr::jsObj($d['grid']) ?>
});
</script>