<?php

trait DdCrudParamFilterCtrl {
use DdCrudCtrl, DdParamFilterCtrl;

  protected function _paramFilterItems() {
    return $this->items();
  }

  protected function oProcessItemsInitParamFilter() {
    $this->initFilterByParams();
  }

}