<?php

class DmfaDdTagsConsecutiveSelect extends DmfaDdTagsTreeSelect {

  /*
  function post2formFormat($v, $k) {
    if (!$v) return null;
    return DdTags::get($this->dm->strName, $k)->getParentIds2($v);
  }
  */

  function source2formFormat($v, $name) {
    return $v ? TreeCommon::flat([$v]) : '';
  }

}