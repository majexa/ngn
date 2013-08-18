<?php

class FieldEWisiwig extends FieldETextarea {

  protected $useDefaultJs = true;

  protected $staticType;

  function defineOptions() {
    return array_merge(parent::defineOptions(), ['rowClass' => 'elWisiwig']);
  }

  protected function addRequiredCssClass() {
    if (!empty($this->options['required'])) $this->cssClasses[] = 'required-wisiwig';
  }

}