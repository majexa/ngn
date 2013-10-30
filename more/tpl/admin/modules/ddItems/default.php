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

    Ngn.GridBtnAction.NewKpOrder = new Class({
      Extends: Ngn.GridBtnAction.New,
      action: function() {
        new Ngn.Dialog.RequestForm({
          width: 300,
          dialogClass: 'dialog fieldFullWidth',
          url: this.grid.options.basePath + '/json_selectCreditType',
          title: false,
          nextFormOptions: this.getDialogOptions()
        });
      }
    });

    var newMenuOption = menu.get('cls', 'add');
    newMenuOption.action = Ngn.GridBtnAction.NewKpOrder;

    var opt = {
      menu: menu,
      toolActions: Ngn.Grid.toolActions,
      isSorting: <?= Arr::jsValue(!empty($d['settings']['enableManualOrder'])) ?>
    };
    Ngn.DdGrid.Admin.grid = new Ngn.DdGrid.Admin.factory(Ngn.getParam(2), opt).reload();

  })();

</script>
