<div id="table"></div>
<script>
  (function() {
    var menu = Ngn.Grid.defaultMenu;
    menu.push({
      title: 'Настройки',
      cls: 'settings',
      action: function() {
        new Ngn.Dialog.RequestForm({
          url: Ngn.getPath(1) + '/ddItems/' + Ngn.getParam(2) + '/json_settings',
          onOkClose: function() {
            window.location.reload();
          }
        });
      }
    });
    var opt = {
      menu: menu,
      toolActions: Ngn.Grid.toolActions,
      isSorting: <?= Arr::jsValue(!empty($d['settings']['enableManualOrder'])) ?>
    };
    Ngn.DdGrid.Admin.grid = new Ngn.DdGrid.Admin.factory(Ngn.getParam(2), opt).reload();
  })();
</script>
