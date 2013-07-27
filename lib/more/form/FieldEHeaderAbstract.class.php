<?php

class FieldEHeaderAbstract extends FieldEAbstract {

  public $options = [
    'noRowHtml' => true,
    'noValue'   => true
  ];

  static $i = 0;

  protected function init() {
    parent::init();
    $this->options['hRequired'] = !empty($this->options['required']);
    $this->options['required'] = false;
    self::$i++;
    if (empty($this->options['name'])) $this->options['name'] = $this->type.self::$i;
  }

  function _html() {
    if (empty($this->options['title'])) return '';
    return '<h3>'.$this->options['title'].(!empty($this->options['hRequired']) ? '{required}' : '').'</h3>';
  }

}
