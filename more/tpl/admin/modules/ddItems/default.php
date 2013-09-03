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
  var opt = {
    menu: menu,
    toolActions: Ngn.Grid.toolActions,
    isSorting: <?= Arr::jsValue(!empty($d['settings']['enableManualOrder'])) ?>
  };
  <? Tt()->tpl('admin/dd/beforeGridInit.js', $d, true) ?>
  var grid = new Ngn.Grid(opt).reload();
  <? Tt()->tpl('admin/dd/afterGridInit.js', $d, true) ?>
</script>
