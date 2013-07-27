<?php

class PcpTpl extends Pcp {
  
  public $title = 'Произвольный шаблон';
  
  function getProperties() {
    return Arr::append(parent::getProperties(), [
      [
       'name' => 'tplName',
        'title' => 'Имя шаблона',
        'type' => 'text',
        'required' => 1
      ]
    ]);
  }
  
}
