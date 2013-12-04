<?php

class FieldESelect extends FieldEText {

  static $requiredOptions = ['name'];

  protected function init() {
    parent::init();
    if (!isset($this->options['options'])) throw new Exception(get_class($this).": options not set in element: ".getPrr($this->options));
    if (!is_array($this->options['options'])) throw new Exception('options[options] is not array. options: '.getPrr($this->options));
    if (empty($this->options['forceAssoc']) and !Arr::isAssoc($this->options['options'])) $this->options['options'] = Arr::toOptions($this->options['options']);
  }

  protected $defaultCaption = null;

  function _html() {
    $opts = [
      'noSelectTag'    => true,
      'defaultCaption' => $this->defaultCaption
    ];
    if (($classes = $this->getCssClasses()) !== false) $opts['class'] = implode(' ', $classes);
    if (is_array($this->options['value'])) $this->options['value'] = null;//throw new Exception('it is array: '.$this->options['name'].'...'.getPrr($this->options['value']));
    return '<select name="'.$this->options['name'].'"'.(isset($this->options['dataParams']) ? Html::dataParams($this->options['dataParams']) : '').Tt()->tagParams($this->getTagsParams()).$this->getClassAtr().' id="'.Misc::name2id($this->options['name']).'i">'.Html::select($this->options['name'], $this->options['options'], $this->options['value'], $opts).'</select>';
  }

  function titledValue() {
    $value = $this->value();
    return isset($this->options['options'][$value]) ? $this->options['options'][$value] : null;
  }

}