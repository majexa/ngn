<?php

class CtrlCommonTinyImageProp extends CtrlCommon {
  
  function action_json_default() {
    $this->json['title'] = 'Параметры изображения';
    return new Form(new Fields([
      [
        'title' => 'Название изображения',
        'name' => 'alt',
        'help' => 'Альтернативный текст (тег "alt")'
      ]
    ]));
  }
  
}