<div class="editBlock smIcons bordered">
  <? if (!$d['active']) { ?>
    <a href="<?= $this->getPath(1) ?>?a=publish&itemId=<?= $d['id'] ?>" class="publish" title="Опубликовать" onclick="if (confirm('Уверены?')) window.location = this.href; return false;"><i></i></a>
  <? } ?>
  <a href="<?= $this->getPath(1) ?>?a=edit&itemId=<?= $d['id'] ?>" class="edit" title="Редактировать"><i></i></a>
  <a href="<?= $this->getPath(1) ?>?a=delete&itemId=<?= $d['id'] ?>" class="delete" title="Удалить" onclick="if (confirm('Уверены?')) window.location = this.href; return false;"><i></i></a>
</div>
