<?php

class FieldEMultiselect extends FieldESelect {

  static protected $dd = false;

  protected function defineOptions() {
    $this->options['minNum'] = 0;
    $this->options['maxNum'] = 0;
  }

  protected function init() {
    if ($this->options['minNum'] != 0) $this->options['required'] = true;
    if (count($this->options['options']) > 20) $this->options['rowClass'] = 'longElement';
    parent::init();
  }

  function _html() {
    $parentId = 'chbxs'.$this->options['name'];
    $input = '<div id="'.$parentId.'">';
    $n = 0;
    foreach ($this->options['options'] as $k => $v) {
      $checked = '';
      if (is_array($this->options['value']) and in_array($k, $this->options['value'])) $checked = ' checked';
      $defaultAttr = ' name="'.$this->options['name'].'[]"';
      $id2 = $this->options['id'].Misc::name2id($k);
      $input .= '<span class="checkbox">'.'<input type="checkbox"'.$defaultAttr. //
        ((!empty($this->options['required']) and !$n) ? ' class="validate-reqchk-bynode nodeId:\''.$parentId.'\'"' : '').' id="'.$id2.'" value="'.$k.'"'.$checked.'> <label for="'.$id2.'">'.$v.'</label></span>';
      $n++;
    }
    $input .= '<div class="clear"><!-- --></div></div>';
    return $input;
  }

  protected function validate2() {
    if ($this->options['maxNum'] and count($this->options['value']) > $this->options['maxNum']) $this->error("Выберите не больше <b>{$this->options['maxNum']}</b> пунктов");
    if ($this->options['maxNum'] and count($this->options['value']) < $this->options['minNum']) $this->error("Выберите хотя бы <b>{$this->options['minNum']}</b> пунктов");
  }

}
