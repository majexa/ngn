<?php

class DmfaDdTagsTreeMultiselectAc extends DmfaDdTagsTreeMultiselect {

  function afterCreateUpdate($value, $k) {
    $ids = [];
    foreach (explode(',', $value) as $v) if (($v = (int)$v)) $ids[] = $v;
    parent::afterCreateUpdate($ids, $k);
  }

}