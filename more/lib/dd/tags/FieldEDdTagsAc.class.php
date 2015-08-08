<?php

class FieldEDdTagsAc extends FieldEText {

  static $ddTags = true, $ddTagsItemsDirected = true, $ddTagsMulti = true;

  protected function defineOptions() {
    return array_merge(parent::defineOptions(), [
      'useTypeJs' => true,
    ]);
  }

}