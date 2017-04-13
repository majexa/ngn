<?php

// Тип ddItemsSelect теперь тэговый. Создаем тэг-группу и создаем тэг-записи из значений
$items = new DdFieldItems;
$items->cond->addF('type', 'ddItemsSelect');
foreach ($items->getItems() as $v) {
  $r = [];
  $r['itemsDirected'] = 0;
  $r['unicalTagsName'] = 0;
  $r['tree'] = 0;
  $r['strName'] = $v['strName'];
  $r['name'] = $v['name'];
  db()->insert('tags_groups', $r);
  $subItems = new DdDbItemsExtended($v['settings']['strName']);
  foreach (db()->select("SELECT id AS itemId, {$v['name']} AS tagId FROM dd_i_{$v['strName']} WHERE {$v['name']}!=0") as $vv) {
    $vv['groupName'] = $v['name'];
    $vv['strName'] = $v['strName'];
    db()->insert('tags_items', $vv);
  }
}

// Добавляем проядок в города
function reorderNodes($nodes, $parentId = 0) {
  foreach ($nodes as $node) {
    if ($node['children']) {
      // меняемм порядок детей текущего нода, если они есть
      DbShift::sort('tagCities', (new DbCond)->addF('parentId', $parentId));
      reorderNodes($node['children'], $node['id']);
    }
  }
}
reorderNodes([(new DbTree('tagCities'))->getTree()]);
