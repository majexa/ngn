<?php

class FieldEFontStyle extends FieldESelect {

  protected function defineOptions() {
    $this->options['options'] = [
      '' => 'по умолчанию',
      'italic' => 'наклонный',
      'normal' => 'обычный',
    ];
  }

}
