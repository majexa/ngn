<?php

class FieldEDdTagsTreeMultiselectDialogable extends FieldEDdTagsTreeMultiselect {

  protected function defineOptions() {
    return array_merge(parent::defineOptions(), [
      'rowClass' => 'treeMultiselectDialogable',
      'useTypeJs' => true
    ]);
  }

  function typeCssAndJs() {
    Sflm::frontend('css')->addLib('i/css/common/treeMultiselectDialogable.css');
    return parent::typeCssAndJs();
  }

}