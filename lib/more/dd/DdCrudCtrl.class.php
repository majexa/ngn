<?php

trait DdCrudCtrl {
use DdCrudAbstractCtrl;

  protected function items() {
    return new DdItems($this->getStrName());
  }

}