<?php

class MultiImageUploadForm extends Form {

  protected function defineOptions() {
    return array_merge(parent::defineOptions(), [
      'idByClass'   => true,
      'jsClassById' => true
    ]);
  }

  function __construct(array $options = []) {
    parent::__construct([
      [
        'title'        => 'Фотографии',
        'name'         => 'images',
        'type'         => 'file',
        'multiple'     => true,
        'allowedMimes' => ['image/gif', 'image/jpeg', 'image/png', 'image/bmp']
      ]
    ], array_merge($options, [
      'submitTitle' => 'Загрузить',
    ]));
    UploadTemp::extendFormOptions($this, ($this->options['baseUrl'] ?: '').'/json_upload');
  }

}