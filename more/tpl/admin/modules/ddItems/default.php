<div id="table"></div>
<script>
  (function() {
    var menu = Ngn.Grid.defaultMenu;
    <? if (Misc::isGod()) { ?>
//    menu.push({
//      title: Ngn.Locale.get('Core.settings'),
//      cls: 'settings',
//      action: function() {
//        new Ngn.Dialog.RequestForm({
//          url: Ngn.Url.getPath(1) + '/ddItems/' + Ngn.Url.getParam(2) + '/json_settings',
//          onOkClose: function() {
//            window.location.reload();
//          }
//        });
//      }
//    });
    <? } ?>
    <? if ($d['enableImageMultiUpload']) { ?>
    menu.push({
      title: 'Multi upload',
      cls: 'add',
      action: function() {
        new Ngn.Dialog.RequestForm({
          url: Ngn.Url.getPath(1) + '/ddItems/' + Ngn.Url.getParam(2) + '/json_imageMultiUpload',
          onOkClose: function() {
            window.location.reload();
          }
        });
      }
    });
    <? } ?>
    var opt = {
      menu: menu,
      toolActions: Ngn.Grid.toolActions,
      isSorting: <?= Arr::jsValue(!empty($d['settings']['enableManualOrder'])) ?>
    };
    Ngn.DdGrid.Admin.grid = new Ngn.DdGrid.Admin.factory('<?= $d['structure']['name'] ?>', opt).reload();
  })();
</script>
