<div id="table"></div>
<script>
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
  new Ngn.Grid({
    menu: menu,
    toolActions: Ngn.Grid.toolActions,
    isSorting: <?= Arr::jsValue(!empty($d['settings']['enableManualOrder'])) ?>
  }).reload();
</script>
