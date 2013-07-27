<?php

class FieldEFileBase extends FieldEInput {

  public $inputType = 'file';
  
  protected $allowedMimes = null;
  
  function defineOptions() {
    $this->options = [
      'help' => 'Максимальный размер '.
        (empty($this->options['multiple']) ? 'файла' : 'файлов').
        ': '.ini_get('upload_max_filesize'),
      'currentFileClass' => 'file'
    ];
  }
  
}