<table cellpadding="0" cellspacing="0" class="valignSimple">
<tr>
  <td><?= $this->tpl('admin/modules/pages/treeEdit', $d) ?></td>
  <td height="100%"><div class="vHandler" id="handler">&nbsp;</div></td>
  <td width="100%">
  <div id="mainListPanel">
    <!-- subNav -->
    <? $this->tpl('admin/modules/pages/header', $d) ?>
    <!-- right scrolling element -->
    <div id="rightPanel">
    <? $this->tpl($d['rightPanelTpl'], $d['rightPanelData']) ?>
    </div>
  </div>
  </td>
</tr>
</table>
