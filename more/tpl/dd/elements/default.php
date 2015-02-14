<?php

/* @var $ddo Ddo */
$ddo = $d['o'];
$extraClasses = [];
if (($itemClasses = Config::getVarVar('dd', 'useFieldNameAsItemClass', true))) {
  foreach ($itemClasses as $v) {
    $extraClasses[] = $v['field'].'_'.Ddo::getFlatValue($d[$v['field']]);
  }
}
print '<div class="item'.($d['active'] ? '' : ' nonActive'). //
  (!empty($d['image']) ? ' isImage' : ''). //
  ($extraClasses ? ' '.implode(' ', $extraClasses) : ''). //
  '" data-id="'.$d['id'].'" data-userId="'.$d['userId'].'">';
print '<div class="itemBody">';
$fields = array_values($ddo->fields);

if ($ddo->groupElementsColsN) {
  for ($n = 0; $n < count(array_values($ddo->fields)); $n++) {
    $field =& $fields[$n];
    if (DdFieldCore::isGroup($field['type']) or !$ddo->groupElements) $group[] = $n;
  }
  $fieldsN = count($group) / $ddo->groupElementsColsN;
}

for ($n = 0; $n < count($fields); $n++) {
  $f =& $fields[$n];
  $f['evenNum'] = $n % 2;
  // Открывающийся тэг группы
  if ($ddo->groupElementsColsN) for($col = 1; $col < $ddo->groupElementsColsN; $col++)  if ($n == $group[$fieldsN*$col])print '</div><!-- Close col --><div class="col col'.$n.'">';
  if ($ddo->groupElementsColsN) if ($n == 0) print '<div class="col col'.$n.'">';
  if ($ddo->groupElements and $n == 0 or DdFieldCore::isGroup($f['type'])) {
    // Если это первый элемент или это элемент после Заголовка
    //print St::dddd($ddo->hgrpBeginDddd, $f);
    print $ddo->hgrpBeginDddd($f['type'], $f['name'], $f['evenNum']);
  }
  $type = DdFieldCore::getType($f['type'], false);
  if (empty($type['noElementTag'])) {
    $el = $d[$f['name']]; // $el содержит текущее значение элемента записи
    print St::dddd($ddo->elBeginDddd, $f);
    print $ddo->el($el, $f['name'], $d['id']);
    print $ddo->elEnd;
  }
  // Закрывающийся тэг группы
  if ($ddo->groupElements and isset($fields[$n + 1]) and
    DdFieldCore::isGroup($fields[$n + 1]['type'])
  ) {
    // Если это последний элемент или элемент перед Заголовком
    print '</div><!-- Close fields group -->';
  }
}
// Закрывающийся тэг группы
if ($ddo->groupElements) print '</div><!-- Close fields group -->';
if ($ddo->groupElementsColsN)  print '</div><!-- Close col -->';
print '<div class="clear"><!-- --></div>';
print '</div>';
print '</div>';