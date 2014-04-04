<?php

trait DdCrudParamFilterCtrl {
use DdCrudCtrl, DdParamFilterCtrl;

  protected function paramFilterItems() {
    return $this->items();
  }

  protected function processItemsInitParamFilter() {
    $this->initFilterByParams();
  }

}