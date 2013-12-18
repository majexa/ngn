<?php

/* @var $ddo Ddo */
$ddo = $d['o'];
$extraClasses = [];
if (($itemClasses = Config::getVarVar('dd', 'useFieldNameAsItemClass', true))) {
  foreach ($itemClasses as $v) {
    $extraClasses[] = $v['field'].'_'.Ddo::getFlatValue($d[$v['field']]);
  }
}

print '<div class="item'.($d['active'] ? '' : ' nonActive').(!empty($d['image']) ? ' isImage' : '').($extraClasses ? ' '.implode(' ', $extraClasses) : '').'" data-id="'.$d['id'].'" data-userId="'.$d['userId'].'">';
print '<div class="itemBody">';
$fields = array_values($ddo->fields);
for ($n = 0; $n < count($fields); $n++) {
  $f =& $fields[$n];
  $f['evenNum'] = $n % 2;
  // Открывающийся тэг группы
  if ($n == 0 or DdFieldCore::isGroup($f['type'])) {
    // Если это первый элемент или это элемент после Заголовка
    //print St::dddd($ddo->hgrpBeginDddd, $f);
    print $ddo->hgrpBeginDddd($f['type'], $f['name'], $f['evenNum']);
  }
  $typeData = DdFieldCore::getTypeData($f['type'], false);
  if (empty($typeData['noElementTag'])) {
    $el = $d[$f['name']]; // $el содержит текущее значение элемента записи
    print St::dddd($ddo->elBeginDddd, $f);
    print $ddo->el($el, $f['name'], $d['id']);
    print $ddo->elEnd;
  }
  // Закрывающийся тэг группы
  if (isset($fields[$n + 1]) and
    DdFieldCore::isGroup($fields[$n + 1]['type'])
  ) {
    // Если это последний элемент или элемент перед Заголовком
    print '</div><!-- Close fields group -->';
  }
}

// Закрывающийся тэг группы
print '</div><!-- Close fields group -->';

print '<div class="clear"><!-- --></div>';
print '</div>';
print '</div>';

