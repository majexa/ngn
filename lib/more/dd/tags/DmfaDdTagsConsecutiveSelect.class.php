<?php

class DmfaDdTagsConsecutiveSelect extends DmfaDdTagsTreeSelect {

  function source2formFormat($v, $name) {
    return $v ? $this->getTags($name)->getParentIds2($v['id']) : '';
  }

}