<?php

trait DdCrudCtrl {
use DdCrudAbstractCtrl;

  protected function _items(array $options = []) {
    return new DdItems($this->getStrName(), $options);
  }

}