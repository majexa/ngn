<?php

class DmfaDdTags extends DmfaDdTagsAbstract {

  function afterCreateUpdate($v, $k) {
    if (is_array($v)) $v = '';
    DdTags::items($this->dm->strName, $k)->create($this->dm->id, Misc::quoted2arr($v));
  }

}