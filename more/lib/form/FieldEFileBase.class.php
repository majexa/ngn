<?php

class FieldEFileBase extends FieldEInput {

  public $inputType = 'file';

  protected $allowedMimes = null;

  function defineOptions() {
    return [
      'help'             => 'Максимальный размер '. //
        (empty($this->options['multiple']) ? 'файла' : 'файлов').': '. //
        preg_replace_callback('/(\d+)(\w+)/', function(array $m) {
          return $m[1].' '.File::$phpIniToHuman[$m[2]];
        }, ini_get('upload_max_filesize')),
      'currentFileClass' => 'file'
    ];
  }

}