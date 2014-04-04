<?php

class DmfaWisiwig extends DmfaWisiwigAbstract {

  function form2sourceFormat($v) {
    if (!$this->dm->typo) return $v;
    if (!Config::getVar('tiny', 'typo')) return $v;
    return O::get('FormatText', [
      'allowedTagsConfigName' => 'tiny.admin.allowedTags'
    ])->html($v);
  }
  
  function elAfterCreate(FieldEWisiwig $el) {
    $this->dm->moveTempFiles($el->options['value'], $this->dm->id, $el['name']);
    $this->dm->cleanupImages($el->options['value'], $this->dm->id, $el['name']);
    $this->dm->items->updateField($this->dm->id, $el['name'], $el->options['value']);
    //$this->dm->updateField($this->dm->id, $el['name'], $el->options['value']);
  }
  
  function elAfterUpdate(FieldEWisiwig $el) {
    $value = BracketName::getValue($this->dm->data, $el['name']);
    $this->dm->cleanupImages($value, $this->dm->id, $el['name']);
    $this->dm->items->updateField($this->dm->id, $el['name'], $value);
    //$this->dm->updateField($this->dm->id, $el['name'], $value);
  }

}
