<?php

class FieldEColor extends FieldEText {

  static $title;

  protected function defineOptions() {
    return array_merge(parent::defineOptions(), [
      'useTypeJs' => true,
    ]);
  }

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

FieldEColor::$title = Locale::get('color');