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
    // Переопределяем для типа кредита
    // clone menu['new'].action;
   //
    //c(menu);
    //for (var i=0; i<menu.length; i++) if (menu[i])

    var newMenuOption = menu.get('cls', 'add');
    //c(newMenuOption.action);

    var ac = new Class({

    });

    c(typeof(ac));

    newMenuOption.action = function (grid) {
      new Ngn.Dialog.RequestForm({
        width: 300,
        dialogClass: 'dialog fieldFullWidth',
        url: grid.options.basePath + '/json_selectCreditType',
        title: false,
        nextFormOptions: {},
        onOkClose: function() {
          c('Это успех');
        }.bind(this)
      });
    };

    /*
    menu['new'].action = function(grid) {
    };
    */

    var opt = {
      menu: menu,
      toolActions: Ngn.Grid.toolActions,
      isSorting: <?= Arr::jsValue(!empty($d['settings']['enableManualOrder'])) ?>
    };
    Ngn.DdGrid.Admin.grid = new Ngn.DdGrid.Admin.factory(Ngn.getParam(2), opt).reload();
  })();
</script>
