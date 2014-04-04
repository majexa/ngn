<?php

class FieldEDdTagsTreeMultiselectDialogable extends FieldEDdTagsTreeMultiselect {

  protected $useTypeJs = true;

  protected function defineOptions() {
    return array_merge(parent::defineOptions(), [
      'rowClass' => 'treeMultiselectDialogable'
    ]);
  }

  function typeJs() {
    Sflm::frontend('css')->addLib('i/css/common/treeMultiselectDialogable.css');
    return parent::typeJs();
  }

}