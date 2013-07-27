<div class="navSub iconsSet">
  <a href="<?= $this->getPath() ?>" class="list"><i></i>Полея структуры «<b><?= $d['strData']['title'] ?></b>»</a>
  <a href="<?= $this->getPath(1) ?>/ddStructure" class="list"><i></i>Структуры</a>
  <a href="<?= $this->getPath() ?>?a=new" class="add"><i></i>Создать поле</a>
  <a href="<?= $this->getPath(1).'/ddStructure?a=edit&id='.$d['strData']['id'] ?>" class="edit"><i></i>Редактировать структуру <b><?= $d['strData']['title']?></b></a>
  <a href="<?= $this->getPath() ?>?a=import" class="import"><i></i>Импорт полей</a>
  <a href="<?= $this->getPath() ?>?a=deleteAll" class="confirm delete"><i></i>Удалить все поля</a>
  <div class="clear"><!-- --></div>
</div>
