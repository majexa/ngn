<?php

class DmfaWisiwigAbstract extends Dmfa {
  
  function form2sourceFormat($v) {
    if (!$this->dm->typo) return $v;
    if (!Config::getVar('tiny', 'typo')) return $v;
    return $formatText = O::get('FormatText', [
      'allowedTagsConfigName' => 'tiny.admin.allowedTags'
    ])->html($v);
  }
  
  /*
  function source2formFormat($v) {
    return htmlspecialchars($v);
  }
  */

}