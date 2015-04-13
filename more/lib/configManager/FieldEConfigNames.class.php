<?php

class FieldEConfigNames extends FieldESelect {

  protected function defineOptions() {
    return [
      'options' => array_merge(['' => '—'], ProjectConfig::getTitles('vars'))
    ];
  }

}