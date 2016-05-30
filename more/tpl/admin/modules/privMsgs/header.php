<div class="navSub iconsSet">
  <a href="<?= $this->getPath(2) ?>" class="list"><i></i><?= Locale::get('messages') ?></a>
  <a href="<?= $this->getPath(2) ?>?a=clear" class="delete"
    onclick="if (confirm('<?= Locale::get('deleteMsgsConfirm') ?>')) window.location = this.href; return false;"><i></i><?= Locale::get('deleteAll') ?></a>
  <a href="<?= $this->getPath(2) ?>/sendPage" class="privMsgs"><i></i>Написать</a>
  <div class="clear"><!-- --></div>
</div>


