<?php

class DmfaDdTagsConsecutiveSelect extends DmfaDdTagsTreeSelect {

  function source2formFormat($v, $name) {
    return $v ? TreeCommon::getFlatParams([$v]) : '';
  }

}