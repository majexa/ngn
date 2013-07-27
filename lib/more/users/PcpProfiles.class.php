<?php

class PcpProfiles extends PcpDdItems {

  public $title = 'Профили';
  
  function getProperties() {
    return array_merge([[
      'title' => 'Раздел профиля',
      'name' => 'myProfileId',
      'type' => 'pageId'
    //)), Arr::dropBySubKey(parent::getProperties(), 'name', 'strName'));
    ]], parent::getProperties());
  }

}
