<?php

class DmfaDdTagsSelect extends DmfaDdTagsAbstract {

  function afterCreateUpdate($v, $k) {
    if (empty($v)) {
      $this->deleteTagItems($k);
    } else {
      DdTags::items($this->dm->strName, $k)->createById($this->dm->id, $v);
    }
  }

}