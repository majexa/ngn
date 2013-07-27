<?php

class FieldEImage extends FieldEFile {

  protected $allowedMimes = ['image/jpeg', 'image/png', 'image/bmp', 'image/gif'];
  
  function defineOptions() {
    parent::defineOptions();
    $this->options['currentFileTitle'] = 'Текущее изображение';
    $this->options['currentFileClass'] = 'image';
  }
  
}
