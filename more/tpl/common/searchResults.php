<? if ($d['items']) { ?>
  <?= Html::select($d['name'], $d['items'], null, ['tagId', $d['name']]); ?>
<? } else { ?>
  Ничего не найдено
<? } ?>