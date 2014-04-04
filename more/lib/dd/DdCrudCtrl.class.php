<?php

trait DdCrudCtrl {
use DdCrudAbstractCtrl;

  protected function _items() {
    return new DdItems($this->getStrName());
  }

}