<div class="consecutiveSelect">
  <?
  $count = count($d['DbItemsExtended']);
  for ($i=0; $i < $count; $i++) {
    print
      Html::select($d['name'], $d['DbItemsExtended'][$i]['options'], $d['DbItemsExtended'][$i]['default'], [
        'class' => $d['required'] ? 'required' : '',
        'data' => ['name' => $d['baseName']]
      ]).
      ($i != $count-1 ? '<div class="arrow"> → </div>' : '');
  }
  ?>
</div>
<div class="clear"><!-- --></div>
