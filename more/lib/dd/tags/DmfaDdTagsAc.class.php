<?php

class DmfaDdTagsAc extends DmfaDdTagsAbstract {

  function source2formFormat($v) {
    return $v ? implode(',', Arr::get($v, 'title')) : '';
  }

  function afterCreateUpdate($v, $k) {
    $ids = explode(',', $v);
    if (!$ids) {
      $this->deleteTagItems($k);
    }
    else {
      $numericIds = array_filter($ids, 'is_numeric');
      if ($numericIds) {
        // by ids
        DdTags::items($this->dm->strName, $k)->createByIds($this->dm->id, $numericIds);
      }
      else {
        // by titles
        DdTags::items($this->dm->strName, $k)->create($this->dm->id, array_filter($ids, function ($value) {
          return !is_numeric($value);
        }));
      }
    }

  }

}