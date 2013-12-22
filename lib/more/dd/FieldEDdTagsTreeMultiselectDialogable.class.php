<?php

class FieldEDdTagsTreeMultiselectDialogable extends FieldEDdTagsTreeMultiselect {

  protected function defineOptions() {
    return array_merge(parent::defineOptions(), [
      'rowClass' => 'treeMultiselectDialogable'
    ]);
  }

  function typeJs() {
    if (!parent::typeJs()) return '';
    Sflm::flm('css')->addLib('i/css/common/treeMultiselectDialogable.css');
  }

}