<div class="items">
  <? foreach ($d as $v) { ?>
    <div class="smIcons item bordered">
      <a href="#" class="edit"><i></i></a>
      <? if (Tpl::getSettings($v)) { ?>
      <a href="<?= $this->getPath(2).'/editSettings/'.Tpl::clearSlashes($v) ?>"
        class="settings"><i></i></a>
      <? } ?>
      <?= $v ?>
      <div class="clear"><!-- --></div>
    </div>
  <? } ?>
</div>