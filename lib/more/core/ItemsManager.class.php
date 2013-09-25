<?php

class ItemsManager extends DataManagerAbstract {
  
  /**
   * @var UpdatableItems
   */
  public $items;
  
  function __construct(UpdatableItems $items, Form $form, array $options = []) {
    $this->items = $items;
    parent::__construct($form, $options);
  }

  public function getItem($id) {
    return $this->items->getItem($id);
  }
  
  protected function _create() {
    return $this->items->create($this->data);
  }
  
  protected function _update() {
    $this->items->update($this->id, $this->data);
  }
  
  protected function _delete() {
    $this->items->delete($this->id);
  }

  function _updateField($id, $fieldName, $value) {
    if (BracketName::getKeys($fieldName) !== false) {
      $data = $this->getItem($id);
      BracketName::setValue($data, $fieldName, $value);
      $this->items->update($id, $data);
    } else {
      $this->items->updateField($id, $fieldName, $value);
    }
  }

}
