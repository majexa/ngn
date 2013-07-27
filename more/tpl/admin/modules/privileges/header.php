<? if ($d['page']) { ?>
<div class="gray"><? $this->tpl('admin/modules/pages/header', $d) ?></div>
<? } ?>
<div class="navSub iconsSet">
  <a href="<?= $this->getPath(2) ?>" class="list"><i></i>Список всех привилегий</a>
  <a href="<?= $this->getPath(3) ?>/new" class="add"><i></i>Добавить привилегии</a>
  <a href="<?= params(2) ? $this->getPath(3).'/pagePrivileges' : $this->getPath(2) ?>?a=cleanup" class="cleanup"><i></i>Очистить пустые привилегии</a>
  <div class="clear"><!-- --></div>
</div>

