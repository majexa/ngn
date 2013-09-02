<div class="consecutiveSelect">
  <?
  $count = count($d['items']);
  for ($i=0; $i < $count; $i++) {
    print
      Html::select($d['name'], $d['items'][$i]['options'], $d['items'][$i]['default'], [
        'class' => $d['required'] ? 'required' : '',
        'data' => ['name' => $d['baseName']]
      ]).
      ($i != $count-1 ? '<div class="arrow"> → </div>' : '');
  }
  ?>
</div>
<div class="clear"><!-- --></div>
