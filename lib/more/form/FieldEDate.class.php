<?php

class FieldEDate extends FieldEAbstract {

  protected function getLastYear() {
    return date('Y');
  }

  static function now() {
    return [date('j'), date('n'), date('Y')];
  }

  function _html() {
    $v = $this->options['value'] ?: [0, 0, 0];
    $opt = [];
    if (!empty($this->options['required'])) $opt['class'] = 'required';
    return
      Html::select($this->options['name'].'[]', Html::defaultOption('—') + Arr::toOptions(range(1, 31)), (int)$v[0], $opt).
      Html::select($this->options['name'].'[]', Html::defaultOption('—') + Config::getVar('ruMonths'), (int)$v[1], $opt).
      Html::select($this->options['name'].'[]', Html::defaultOption('—') + Arr::toOptions(array_reverse(range(1900, $this->getLastYear()))), $v[2], $opt);
  }

}
