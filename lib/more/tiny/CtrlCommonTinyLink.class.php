<?php

class CtrlCommonTinyLink extends CtrlCommon {
  
  function action_json_default() {
    $this->json['title'] = 'Вставка ссылки';
    return new Form(new Fields([
      [
        'title' => 'Ссылка',
        'name' => 'link',
        //'type' => 'link',
        //'type' => 'pageLink',
        'required' => true
      ],
      /*
      array(
        'title' => 'Текст ссылки',
        'name' => 'title',
        'required' => true
      )
      */
    ]), [
      'submitTitle' => 'Вставить'
    ]);
  }
  
}