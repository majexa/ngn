<?php

class FieldENumberRange extends FieldEInput {

  function _html() {
    $classAtr = $this->getClassAtr();
    if (!empty($this->options['value'])) {
      $valueFrom = 'value="'.$this->options['value']['from'].'"';
      $valueTo = 'value="'.$this->options['value']['to'].'"';
    } else {
      $valueFrom = '';
      $valueTo = '';
    }
    return <<<TEXT
<input type="text" name="{$this->options['name']}[from]" $valueFrom style="width:100px" $classAtr /> —
<input type="text" name="{$this->options['name']}[to]" $valueTo style="width:100px" $classAtr />
TEXT;
  }

}