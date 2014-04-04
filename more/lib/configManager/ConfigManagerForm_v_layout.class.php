<?php

class ConfigManagerForm_v_layout extends ConfigManagerForm {
  
  protected $maxImageSize = [
    'v[logoImage]' => [200, 50]
  ];
  
  protected function afterUpdate(array $values) {
    //die2($values);
  }
  
}
