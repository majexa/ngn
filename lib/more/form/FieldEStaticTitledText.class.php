<?php

class FieldEStaticTitledText extends FieldEAbstract {

  function _html() {
    return '<span class="text">'.$this->options['text'].'</span>';
  }

}