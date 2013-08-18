<?php

class FieldEFontWeight extends FieldESelect {

  protected function defineOptions() {
    return [
      'options' => [
        ''       => 'по умолчанию',
        'bold'   => 'жирный',
        'normal' => 'обычный'
      ]
    ];
  }

}
