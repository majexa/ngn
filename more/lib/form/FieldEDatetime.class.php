<?php

class FieldEDatetime extends FieldEDate {

  function _html() {
    $html = parent::_html();
    $v = $this->options['value'] ?: [1, 1, $this->getLastYear(), '10', '00'];
    $html .= '<input type="text" name="'.$this->options['name'].'[]" value="'.$v[3].'" maxlength="2" /> : ';
    $html .= '<input type="text" name="'.$this->options['name'].'[]" value="'.$v[4].'" maxlength="2" />';
    return $html;
  }

}