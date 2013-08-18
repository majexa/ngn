<?php

class FieldEFiles extends FieldEFileBase {

  function defineOptions() {
    return array_merge(parent::defineOptions(), ['currentFileTitle' => 'Текущие файлы']);
  }

}
