<?php

trait DbCrudCtrl {
  use CrudAbstractCtrl;

  abstract protected function getFields();

  protected function _getIm() {
    $form = new Form(new Fields($this->getFields()));
    return $this->objectProcess(new DbItemsManager($this->items(), $this->objectProcess($form, 'form')), 'im');
  }

  abstract protected function table();

  /**
   * @abstract
   * @param array|null $options
   * @return DbItems
   */
  protected function _items(array $options = []) {
    return DbItems($this->table(), $options);
  }

  /**
   * @param array $options
   * @return DbItems
   */
  protected function items(array $options = []) {
    if (isset($this->items)) return $this->items;
    $this->items = $this->_items($options);
    $this->items->hasPagination = true;
    return $this->objectProcess($this->items, 'items');
  }

}