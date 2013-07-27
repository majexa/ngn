<?php

class DbModelManager extends DataManagerAbstract {

  protected $modelName;

  function __construct($modelName, Form $form) {
    $this->modelName = $modelName;
    parent::__construct($form);
  }
  
  protected function _create() {
    return DbModelCore::create($this->modelName, $this->data);
  }
  
  protected function _update() {
    DbModelCore::update($this->modelName, $this->id, $this->data);
  }
  
  protected function getItem($id) {
    return DbModelCore::get($this->modelName, $id)->r; 
  }
  
  protected function _delete() {
    DbModelCore::delete($this->modelName, $this->id);
  }
  
  function updateField($id, $fieldName, $value) {
    DbModelCore::update($this->modelName, $id, [$fieldName => $value]);
  }
  
  function getAttacheFolder() {
    return 'model/'.$this->modelName.'/'.$this->id;
  }

}
