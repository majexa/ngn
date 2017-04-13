<? if ($d['DbItemsExtended']) { ?>
  <?= Html::select($d['name'], $d['DbItemsExtended'], null, ['tagId', $d['name']]); ?>
<? } else { ?>
  Ничего не найдено
<? } ?>