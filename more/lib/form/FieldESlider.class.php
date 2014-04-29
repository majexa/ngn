<?php

class FieldESlider extends FieldEText {

  public $inputType = 'hidden';
  public $useTypeJs = true;

  public $options = [
    'jsOptions' => [
      'steps' => 20,
      'range' => [1, 100]
    ]
  ];

  function setOptions(array $options) {
    parent::setOptions($options);
    foreach (['steps', 'range'] as $k) if (isset($this->options[$k])) {
      $this->options['jsOptions'][$k] = $this->options[$k];
      unset($this->options[$k]);
    }
  }

  //function jsInline() {
    //return 'Ngn.Form.elOptionsName["'.$this->options['name'].'"] = '.json_encode($this->options['jsOptions']).";\n";
  //}

  function _html() {
    return '<div class="slider"><div class="knob"></div></div>'.parent::_html();
  }

}