<?php

$links[] = [
  'title' => 'Слайсы',
  'class' => 'list',
  'link' => $this->getPath(2)
];

/*
if ($d['params'][0] == 'god') {
  $links[] = array(
    'title' => 'Создать глобальный слайс',
    'class' => 'add',
    'link' => $this->getPath(3).'/new'
  );
}
*/

if ($d['params'][0] == 'god' and isset($d['slice'])) {
  $links[] = [
    'title' => 'Удалить этот слайс',
    'class' => 'delete confirm',
    'link' => $this->getPath(4).'?a=delete'
  ];
}
?>

<div class="navSub iconsSet" id="subNav">
  <div class="navSubBtns"">
  <? foreach ($links as $v) { ?>
    <a href="<?= $v['link'] ?>" class="tooltip <?= $v['class'] ?>"<?= isset($v['target']) ? ' target="'.$v['target'].'"' : '' ?><?= isset($v['descr']) ? 'title="'.$v['descr'].'"' : '' ?>><i></i><?= $v['title'] ?></a>
  <? } ?>
  <div class="clear"><!-- --></div>
  </div>
</div>
