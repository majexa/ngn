<?php

class FieldEDdStaticText extends FieldEAbstract {

  public $options = [
    'noRowHtml' => true,
    'noValue' => true
  ];
	
  function html() {
    return '<div class="staticText">'.$this->options['help'].'</div>';
  }

}