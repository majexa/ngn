<?php

class FieldEWisiwig extends FieldETextarea {

  protected $useDefaultJs = true;

  protected $staticType;

  function defineOptions() {
    parent::defineOptions();
    $this->options['rowClass'] = 'elWisiwig';
  }

  protected function addRequiredCssClass() {
    if (!empty($this->options['required'])) $this->cssClasses[] = 'required-wisiwig';
  }

}