<? $this->tpl('admin/modules/users/profileHeader') ?>
<? if ($d['saved']) { ?>
  <p>Информация была сохранена</p>
  <p><a href="<?= $this->getPath() ?>">← вернуться</a></p>
<? } else { ?>
  <div class="apeform"><?= $d['form'] ?></div>
<? } ?>