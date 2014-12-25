<?php

class FieldEWisiwig extends FieldETextarea {

  protected $staticType;

  function defineOptions() {
    return array_merge(parent::defineOptions(), [
      'useTypeJs' => true,
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