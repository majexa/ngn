<?php

class DmfaDdTagsTreeMultiselectAc extends DmfaDdTagsTreeMultiselect {

  function post2formFormat($v, $k) {
    if (!$v) return null;
    $tags = DdTags::get($this->dm->strName, $k);
    $ids = explode(',', $v);
    $tags->getSelectCond()->addF('id', $ids);
    return $tags->getData();
  }

  function source2formFormat($v) {
    if (!$v) return '';
    $r = [];
    foreach ($v as $collection) $r[] = Arr::last($collection);
    return $r;
  }

  function form2sourceFormat($v) {
    return $v;
  }

  function afterUpdate($tags, $k) {
    if (!is_array($tags)) die2($tags);
    parent::afterUpdate(Arr::get($tags, 'id'), $k);
  }

  function afterCreate($tags, $k) {
    parent::afterCreate(Arr::get($tags, 'id'), $k);
  }

}