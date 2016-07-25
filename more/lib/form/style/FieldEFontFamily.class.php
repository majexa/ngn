<?php

class FieldEFontFamily extends FieldESelect {

  static $title;

  protected function defineOptions() {
    return array_merge(parent::defineOptions(), [
      'options' => array_merge(['' => 'по умолчанию'], Arr::toOptions([
        'Times New Roman',
        'Arial',
        [
          'Arial Narrow',
          'Arial Narrow, Liberation Sans Narrow'
        ],
        'Tahoma',
        'Georgia',
        'Courier New',
      ]))
    ]);
  }

}

FieldEFontFamily::$title = Locale::get('font');