<?php

class ItemsManager extends DataManagerAbstract {
  
  /**
   * @var UpdatableItems
   */
  public $oItems;
  
  function __construct(UpdatableItems $items, Form $form, array $options = []) {
    $this->oItems = $items;
    parent::__construct($form, $options);
  }

  protected function getItem($id) {
    return $this->oItems->getItem($id);
  }
  
  protected function _create() {
    return $this->oItems->create($this->data);
  }
  
  protected function _update() {
    $this->oItems->update($this->id, $this->data);
  }
  
  protected function _delete() {
    $this->oItems->delete($this->id);
  }

  protected function _updateField($id, $fieldName, $value) {
    if (BracketName::getKeys($fieldName) !== false) {
      $data = $this->getItem($id);
      BracketName::setValue($data, $fieldName, $value);
      $this->oItems->update($id, $data);
    } else {
      $this->oItems->updateField($id, $fieldName, $value);
    }
  }

}
