<?php

class FieldEFontWeight extends FieldESelect {

  protected function defineOptions() {
    $this->options['options'] = [
      '' => 'по умолчанию',
      'bold' => 'жирный',
      'normal' => 'обычный'
    ];
  }

}
