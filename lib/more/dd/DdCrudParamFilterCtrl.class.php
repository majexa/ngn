<?php

trait DdCrudParamFilterCtrl {
use DdCrudCtrl, DdParamFilterCtrl;

  protected function paramFilterItems() {
    return $this->getItems();
  }

  protected function processItemsInitParamFilter() {
    $this->initFilterByParams();
  }

}