<?php

class FieldECheckbox extends FieldEInput {

  public $inputType = 'checkbox';
  public $markerHtml = '';

  function _html() {
    $input = '';
    $i = 0;
    foreach ($this->options['options'] as $key => $value) {
      $input .= '<span class="'.$this->inputType.((strstr($key, 'disabled')) ? '  disable' : '').(' opt_'.Misc::name2id($key)).'">'.$this->markerHtml.
        '<input type="'.$this->inputType.'"'.' name="'.$this->options['name'].'"'.
        ($this->inputType == 'checkbox' and count($this->options['options']) > 1 ? '[]' : '').
        (($this->options['required'] and $this->inputType == 'checkbox') ? ' class="validate-required-check"' : '').
        ((strstr($key, 'disabled')) ? '  disabled' : '').
        ' id="'.$this->options['id'].Misc::name2id($key).'"';
      $input .= ' value="'.$key.'"';
      if (in_array($key, (array)$this->options['value'])) $input .= ' checked="checked"'.$this->options['value'];
      $input .= ' />';
      $input .= '<label for="'.$this->options['id'].Misc::name2id($key).'"'.'>'.$value./*($this->options['required'] ? '{required}' : '').*/"</label></span>";
      $i++;
    }
    $input .= '<div class="clear"><!-- --></div>';
    return $input;
  }

}