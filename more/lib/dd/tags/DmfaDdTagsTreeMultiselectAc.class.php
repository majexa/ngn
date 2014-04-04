<?php

class DmfaDdTagsTreeMultiselectAc extends DmfaDdTagsTreeMultiselect {

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
    if (empty($tags)) parent::afterUpdate(null, $k);
    else {
      parent::afterUpdate(explode(',', $tags), $k);
    }
  }

  function afterCreate($tags, $k) {
    if (empty($tags)) return;
    parent::afterCreate(explode(',', $tags), $k);
  }

}