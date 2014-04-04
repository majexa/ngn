<?php

class DmfaDdTagsMultiselect extends DmfaDdTagsAbstract {

  function source2formFormat($v) {
    return $v ? Arr::get($v, 'id') : '';
  }

  function afterCreateUpdate($v, $k) {
    // Если данные этого поля пустые
    if (empty($v)) {
      $this->deleteTagItems($k);
    } else {
      DdTags::items($this->dm->strName, $k)->createByIds($this->dm->id, (array)$v);
    }
  }

}