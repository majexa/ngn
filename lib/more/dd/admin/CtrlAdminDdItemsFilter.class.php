<?php

class CtrlAdminDdItemsFilter extends CtrlAdminDdItems {
  use DdParamFilterCtrl, LongJobCtrl;

  protected function paramFilterItems() {
    return $this->getItems();
  }

  protected function paramFilterN() {
    return 3;
  }

  /**
   * @return DdXls
   */
  protected function getLongJob() {
    $this->initFilterByParams();
    return O::gett('DdXls', $this->getItems()->strName, $this->getItems());
  }

  protected function init() {
    parent::init();
    $this->initFilterByParams();
    $filters = [];
    if (($paths = Hook::paths('dd/admin/beforeInitFilters')) !== false) foreach ($paths as $path) include $path;
    $filters = Arr::append($filters, DdGridFilters::getAll($this->getStrName()));
    //if (($paths = Hook::paths('dd/admin/afterInitFilters')) !== false) foreach ($paths as $path) include $path;
    $this->d['filtersForm'] = (new DdGridFilters($filters, $this->getStrName()))->form->html();
    $this->d['bodyClass'] = 'noOverflow';
  }

}