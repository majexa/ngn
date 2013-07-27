<?php

class DmfaDdTagsTreeMultiselectAc extends DmfaDdTagsTreeMultiselect {

  protected function deleteTagItems_($k) {
    die2($k);
  }

  /*
  function afterCreateUpdate($value, $k) {
    if ($value) die2([$k, $value]);
    $this->deleteTagItems($k);
  }
  */

  function afterCreateUpdate($value, $k) {
    $ids = [];
    foreach (explode(',', $value) as $v) if (($v = (int)$v)) $ids[] = $v;
    parent::afterCreateUpdate($ids, $k);
  }

}