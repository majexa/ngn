<?php

class FieldEFontStyle extends FieldESelect {

  protected function defineOptions() {
    return [
      'options' => [
        ''       => 'по умолчанию',
        'italic' => 'наклонный',
        'normal' => 'обычный',
      ]
    ];
  }

}
