<?php

abstract class FieldEInput extends FieldEAbstract {

  const defaultMaxLength = 1000;

  public $inputType;

  protected function getClassAtr() {
    if (($classes = $this->getCssClasses()) !== false) return ' class="'.implode(' ', $classes).'"';
    return '';
  }

  protected function getTagsParams() {
    $opts = $this->options;
    if (isset($opts['value'])) $opts['value'] = $this->prepareInputValue($this->postValue());
    $opt = Arr::filterByKeys($opts, $this->allowedTagParams());
    if (!empty($opts['data'])) foreach ($opts['data'] as $k => $v) $opt["data-$k"] = $v;
    if (!empty($this->options['multiple'])) $opts['multiple'] = null;
    htmlspecialcharsR($opt);
    return $opt;
  }

  protected function allowedTagParams() {
    return ['name', 'maxlength', 'value', 'disabled', 'placeholder', 'autocomplete'];
  }

  protected function prepareInputValue($value) {
    return $value;
  }

  protected function inputId() {
    return Misc::name2id($this->options['name']).'i';
  }

  function _html() {
    return //
      '<input size="40" type="'.$this->inputType.'" '.'id="'.$this->inputId().'"'. //
      Tt()->tagParams($this->getTagsParams()).
      $this->getClassAtr().' />';
  }

}