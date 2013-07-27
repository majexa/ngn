<?php

class PcpMyProfile extends PcpDd {

  public $title = 'Мой профиль';

  public $editebleContent = false;

  function getProperties() {
    $pr = Arr::dropBySubKeys(parent::getProperties(), 'name', 'premoder');
    return Arr::append($pr, [
      [
        'name' => 'mastersProfile', 
        'title' => 'Заголовок во множественном числе' 
      ],
      [
        'name' => 'pluralTitle', 
        'title' => 'Заголовок во множественном числе' 
      ],
    ]);
  }

}
