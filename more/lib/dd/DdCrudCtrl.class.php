<?php

trait DdCrudCtrl {
use DdCrudAbstractCtrl;

  protected function _items(array $options = []) {
    return new DdDbItemsExtended($this->getStrName(), $options);
  }

}