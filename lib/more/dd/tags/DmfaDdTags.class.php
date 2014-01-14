<?php

class DmfaDdTags extends DmfaDdTagsAbstract {

  function source2formFormat($v) {
    return $v ? implode(',', Arr::get($v, 'title')) : '';
  }

  function form2sourceFormat($v) {
    return Misc::quoted2arr(trim($v));
  }

  //function afterCreateUpdate($v, $k) {
  //  if (is_array($v)) $v = '';
  //  DdTags::items($this->dm->strName, $k)->create($this->dm->id, Misc::quoted2arr($v));
  //}

}