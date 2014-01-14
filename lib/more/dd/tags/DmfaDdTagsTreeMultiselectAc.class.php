<?php

class DmfaDdTagsTreeMultiselectAc extends DmfaDdTagsTreeMultiselect {

  function source2formFormat($v) {
    return $v;
  }

  function afterCreateUpdate($value, $k) {
    $ids = [];
    foreach (explode(',', $value) as $v) if (($v = (int)$v)) $ids[] = $v;
    parent::afterCreateUpdate($ids, $k);
  }

}