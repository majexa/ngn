<style>
  #user_message {
    padding-bottom: 10px;
  }
</style>
<div id="table"></div>
<script>
var w = 300;
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
          width: w,
          id: 'user',
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
        width: w,
        id: 'user',
        onOkClose: function() {
          this.reload();
        }.bind(this)
      });
    }
  },
  data: <?= Arr::jsObj($d['grid']) ?>
});
</script>