<?

$titles = [
  'news' => 'Добавить новость',
];

?>
<div class="moderNav">
  <? if ($d['ddType'] == 'ddObjects') { ?>
    <a href="<?= $this->getPath(1) ?>?a=new" class="add"
    title="<?= $titles[$d['ddName']] ? $titles[$d['ddName']] : 'Добавить запись' ?>">
    <i></i><?= $titles[$d['ddName']] ? $titles[$d['ddName']] : 'Добавить запись' ?></a>
  <? } elseif ($d['ddType'] == 'ddStatic') { ?>
    <a href="<?= $this->getPath(1) ?>?a=edit" class="edit"
    title="<?= $titles[$d['ddName']] ? $titles[$d['ddName']] : 'Редактировать' ?>">
    <i></i><?= $titles[$d['ddName']] ? $titles[$d['ddName']] : 'Редактировать' ?></a>
  <? } else { ?>
    <a href="<?= $this->getPath() ?>?a=new" class="add" title="Добавить запись"><i></i>Добавить запись</a>
  <? } ?>
</div>
