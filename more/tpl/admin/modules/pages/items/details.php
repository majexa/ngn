<style>
  .label.sel {
    font-weight: bold;
  }
</style>
<div id="table"></div>
<script>
  var initGrid = function() {

    Ngn.DdGrid.Admin = new Class({
      Extends: Ngn.DdGrid,
      initPagination: function(data, fromAjax) {
        this.parent(data, fromAjax);
        this.ePagination.inject(document.getElement('.pagePath'), 'top').addClass('pNumsTop');
      }
    });

    var opt = {
      basePath: Ngn.getPath(3),
      filterPath: new Ngn.DdFilterPath(4),
      listAction: 'editContent',
      idParam: 'itemId',
      toolActions: {
        edit: function(row) {
          new Ngn.Dialog.RequestForm({
            url: Ngn.getPath(4) + '?a=json_edit&itemId=' + row.id,
            reduceHeight: true,
            title: false,
            onOkClose: function() {
              this.reload(row.id);
            }.bind(this)
          });
        }
      },
      toolLinks: {
        edit: function(row) {
          return Ngn.getPath(4) + '?a=edit&itemId=' + row.id;
        }
      },
      data: <?= Arr::jsObj($d['grid']) ?>
    };

    <? Tt()->tpl('admin/dd/beforeGridInit.js', $d, true) ?>
    var grid = new Ngn.DdGrid.Admin(opt);
    <? Tt()->tpl('admin/dd/afterGridInit.js', $d, true) ?>

    if (Ngn.filtersForm) Ngn.filtersForm.addEvent('jsComplete', function() {
      new Ngn.DdFilterPath.Interface(grid, Ngn.filtersForm);
    });
    Ngn.DdFilterPath.reformat = function(s) {
      s = basename(s);
      return s.replace(/.*_(\d+)-(\d+)-(\d+)_(\d+)-(\d+)-(\d+)/, '$1.$2.$3 $4:$5');
    };
    var job = new Ngn.cp.LongJob({
      title: 'Выгрузка',
      url: Ngn.DdFilterPath.getUrl(),
      action: 'xls',
      completeText: function(r) {
        return '<a href="' + r.data + '">' + Ngn.DdFilterPath.reformat(r.data) + '</a>'
      }
    });
    $('subNav').getElement('.xls').addEvent('click', function(e) {
      e.preventDefault();
      job.options.url = Ngn.DdFilterPath.getUrl();
      job.start();
    });

  };
  window.addEvent('domready', function() {
    initGrid();
  });
</script>