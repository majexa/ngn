<?php

class FieldEDdTagsTreeMultiselectDialogable extends FieldEDdTagsTreeMultiselect {

  protected function defineOptions() {
    return array_merge(parent::defineOptions(), [
      'rowClass' => 'treeMultiselectDialogable',
      'useTypeJs' => true
    ]);
  }

  function typeJs() {
    Sflm::frontend('css')->addLib('i/css/common/treeMultiselectDialogable.css');
    return parent::typeJs();
  }

}