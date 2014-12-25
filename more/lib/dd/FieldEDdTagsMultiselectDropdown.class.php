<?php

class FieldEDdTagsMultiselectDropdown extends FieldEDdTagsMultiselect {

  protected function defineOptions() {
    return array_merge(parent::defineOptions(), [
      'useTypeJs' => true,
    ]);
  }

}
