<?php

trait ElNumberLimits {

  protected $helpEmpty;

  function numberLimits() {
    $helpEmpty = empty($this->options['help']);
    if (!empty($this->options['min'])) {
      $this->cssClasses[] = 'validate-num-min';
      $this->options['data']['min'] = $this->options['min'];
      if ($this->helpEmpty) $this->options['help'] .= 'от '.$this->options['min'].' ';
    }
    if (!empty($this->options['max'])) {
      $this->cssClasses[] = 'validate-num-max';
      $this->options['data']['max'] = $this->options['max'];
      if ($this->helpEmpty) $this->options['help'] .= 'до '.$this->options['max'].' ';
    }
  }

  protected function validate2() {
    if (!empty($this->options['max']) and $this->options['value'] > $this->options['max'])
      $this->error("Превышено максимальное значение {$this->options['max']}");
    if (!empty($this->options['min']) and $this->options['value'] < $this->options['min'])
      $this->error("Минимальное значение: {$this->options['min']}");
  }

}