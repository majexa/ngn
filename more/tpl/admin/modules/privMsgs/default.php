<? $this->tpl('admin/modules/privMsgs/header') ?>
<?/*
<script>
  (function() {
    throw new Error('it is a chack andreay');
  }).delay(100);
  (function() {
    throw new Error("it is a second\nchack andreay");
  }).delay(200);
</script>
*/?>
<? if ($d['msgs']) { ?>
<table cellpadding="0" cellspacing="0" id="itemsTable" class="valign">
<? foreach ($d['msgs'] as $k => $v) { ?>
  <tr class="<?= $v['viewed'] ? '' : 'notViewed' ?>">
    <td>
      <a class="iconBtn delete" title="<?= LANG_DELETE ?>"
        href="<?= $this->getPath() ?>?a=delete&id=<?= $v['id'] ?>"
        onclick="if (confirm('<?= LANG_ARE_YOU_SURE ?>')) window.location = this.href; return false;"><i></i></a>
    </td>
    <td>
      <?= $v['text']?>
      <?php /*
      <p><a href="<?= $this->getPath(2).'/sendPage?userId='.$v['fromUserId'] ?>"><b>Ответить</b></a></p>
      */?>
    </td>
    <td nowrap><small><?= date('d.m.Y H:i:s ', $v['time1']) ?></small></td>
  </tr>
<? } ?>
</table>
<? } else { ?>
  <p><?= LANG_YOU_HAVE_NO_MESSAGES ?></p>
<? } ?>