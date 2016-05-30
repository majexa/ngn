<?php

class FieldEImage extends FieldEFile {

  function defineOptions() {
    return array_merge(parent::defineOptions(), [
      'allowedMimes' => ['image/jpeg', 'image/png', 'image/bmp', 'image/gif']
      //'currentFileTitle' => 'Текущее изображение',
      //'currentFileClass' => 'image'
    ]);
  }

  protected $iconClass = 'image';

}
