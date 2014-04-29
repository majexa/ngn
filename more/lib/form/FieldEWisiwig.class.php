<?php

class FieldEWisiwig extends FieldETextarea {

  public $useTypeJs = true;

  protected $staticType;

  function defineOptions() {
    return array_merge(parent::defineOptions(), [
      'rowClass' => 'elWisiwig',
      'jsOptions' => [
        'tinySettings' => [
          'content_css' => Sflm::lib('css')->getUrl('tinyContent')
        ]
      ]
    ]);
  }

  protected function addRequiredCssClass() {
    if (!empty($this->options['required'])) $this->cssClasses[] = 'required-wisiwig';
  }

}