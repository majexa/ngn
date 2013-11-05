<table cellpadding="0" cellspacing="0" class="valignSimple">
  <tr>
    <td id="ddFilters" class="filters"><div class="cont"><?= $d['filtersForm'] ?></div></td>
    <td height="100%">
      <div class="vHandler" id="handler">&nbsp;</div>
    </td>
    <td width="100%">
      <? $this->tpl('admin/modules/ddItems/default', $d) ?>
      <script>
        (function() {
          var filtersForm = Ngn.Form.factory($('ddFilters').getElement('form'));
          if (filtersForm) {
            filtersForm.addEvent('jsComplete', function() {
              new Ngn.DdFilterPath.Interface(Ngn.DdGrid.Admin.grid, this);
            });
          }
          Ngn.DdFilterPath.reformat = function(s) {
            s = basename(s);
            return s.replace(/.*_(\d+)-(\d+)-(\d+)_(\d+)-(\d+)-(\d+)/, '$1.$2.$3 $4:$5');
          };
          new Ngn.cp.TwoPanels($('ddFilters'), Ngn.DdGrid.Admin.grid.eParent, $('handler'), {storeId: 'pagesInterface'});
        })();
      </script>
    </td>
  </tr>
</table>