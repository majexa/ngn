<?php

class FieldEFontFamily extends FieldESelect {

  static $title = 'Шрифт';

  protected function defineOptions() {
    return [
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
    ];
  }

}
