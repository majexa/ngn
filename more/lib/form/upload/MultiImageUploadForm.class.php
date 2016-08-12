<?php

class MultiImageUploadForm extends Form {

  protected function defineOptions() {
    return array_merge(parent::defineOptions(), [
      'title' => Locale::get('upload'),
      'baseUrl' => null,
      'submitTitle' => Locale::get('upload')
    ]);
  }

  function __construct(array $options = []) {
    parent::__construct([
      [
        'title'        => Locale::get('images', 'form'),
        'name'         => 'images',
        'type'         => 'file',
        'multiple'     => true,
        'allowedMimes' => ['image/gif', 'image/jpeg', 'image/png', 'image/bmp']
      ]
    ], $options);
    UploadTemp::extendFormOptions($this, ($this->options['baseUrl'] ?: '').'/json_upload');
  }

}