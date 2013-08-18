<?php

class FieldEImage extends FieldEFile {

  protected $allowedMimes = ['image/jpeg', 'image/png', 'image/bmp', 'image/gif'];
  
  function defineOptions() {
    return array_merge(parent::defineOptions(), [
      'currentFileTitle' => 'Текущее изображение',
      'currentFileClass' => 'image'
    ]);
  }
  
}
