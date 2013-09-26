<?php

class FieldEColor extends FieldEText {

  static $title = 'Цвет';

  protected $useDefaultJs = true;

  function _html() {
    return Tt()->getTpl('common/colorPicker',
      [
        'default' => $this->options['value'], 
        'name' => $this->options['name'],
        'classAtr' => $this->getClassAtr()
      ]
    );
  }
  
}