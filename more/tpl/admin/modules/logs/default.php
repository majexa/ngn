<? $this->tpl('admin/modules/logs/header', $d) ?>
<table cellpadding="0" cellspacing="0" class="itemsTable valign">
<thead>
  <tr>
  </tr>
</thead>
<tbody>
<? foreach ($d['items'] as $k => $v) { ?>
  <tr>
    <td nowrap><small><?= datetimeStr($v['time']) ?></small></td>
    <td>
      <?= $v['body'] ?>
      <?= Tpl::ol(explode("\n", trim($v['trace']))) ?>
      <?= $v['url'] ? '<br><b>URL:</b> '.$v['url'] : '' ?>
      <?= $v['referer'] ? '<br><b>Referer:</b> '.$v['url'] : '' ?>
      <?= $v['post'] ? '<pre>'.$v['post'].'</pre>' : '' ?>
    </td>
  </tr>
<? } ?>
</tbody>
</table>

