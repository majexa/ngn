<?php
  
class DdImportField extends DdFields {
  
  function __construct($strName) {
    parent::__construct($strName);
    $this->getSystem = false;
  }

  function getFields() {
    $fields = [];
    foreach (parent::getFields() as $k => $v) {
      if (!$this->isFileType($v['type']))
        $fields[$k] = $v;
    }
    return $fields;
  }

}