<?php

abstract class DdImportDataReceiver {
  
  /**
   * @var DdImportField
   */
  protected $oF;
  
  protected $fieldTypes;
  
  function __construct(DdImportField $oF) {
    $this->oF = $oF;
    $this->fieldTypes = Arr::get($this->oF->getFields(), 'type', 'name');    
  }
  
  /**
   * @return DdImportField
   */
  function getFieldObj() {
    return $this->oF;
  }
  
  abstract function getData();
  
}
