<?php

class FieldETime extends FieldEText {

  protected function defineOptions() {
    return array_merge(parent::defineOptions(), [
      'maxlength' => 5,
      'help'      => 'Формат: ЧЧ:ММ'
    ]);
  }

  protected $m;

  protected function init() {
    parent::init();
    if (empty($this->options['value'])) $this->options['value'] = '00:00';
    if (preg_match('/(\d+):(\d+)/', $this->options['value'], $this->m)) {
      $this->options['value'] = sprintf("%02s:%02s", $this->m[1], $this->m[2]);
    }
  }

  function validate2() {
    if (empty($this->m)) {
      $this->error = 'Введите правильное время';
      return;
    }
    if ($this->m[1] < 0 or $this->m[1] >= 24 and $this->m[2] < 0 or $this->m[2] >= 60) {
      $this->error = 'Введите правильное время';
    }
  }

}