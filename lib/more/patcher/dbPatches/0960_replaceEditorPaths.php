<?php

//O::get('DbItems', 'dd_structures')->getItems()
foreach (DdStructure::getStructures() as $v) {
  $o = new DdFields($v['name']);
  if (($wisiwigFields = Arr::filterByValue($o->getFields(), 'type', 'wisiwig')) === true) {
    foreach (Arr::get($wisiwigFields, 'name') as $name) {
      db()->query("UPDATE {$v['table']} SET $name=replace($name, '/u/editor/', '/u/ed/');");
    }
  }
}
