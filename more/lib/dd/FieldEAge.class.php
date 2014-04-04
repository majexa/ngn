<?php

class FieldEAge extends FieldENum {

  function validate1() {
    if ($this->options['value'] < 10 or $this->options['value'] > 50)
      $this->error('Ты слишком молод или стар');
  }

}