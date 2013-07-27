<?php

class FieldEFiles extends FieldEFileBase {

  function defineOptions() {
    parent::defineOptions();
    $this->options['currentFileTitle'] = 'Текущие файлы';
  }

}
