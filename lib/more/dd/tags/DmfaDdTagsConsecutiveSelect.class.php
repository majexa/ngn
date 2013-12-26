<?php

class DmfaDdTagsConsecutiveSelect extends DmfaDdTagsTreeSelect {

  function source2formFormat($v, $name) {
    return $this->getTags($name)->getParentIds2($v['id']);
  }

}