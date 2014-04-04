<?php

class CtrlAdminDdItemsFilter extends CtrlAdminDdItems {
  use DdParamFilterCtrl, LongJobCtrl;

  protected function paramFilterItems() {
    return $this->items();
  }

  protected function paramFilterN() {
    return 3;
  }

  /**
   * @return DdXls
   */
  protected function getLongJob() {
    $this->initFilterByParams();
    return O::gett('DdXls', $this->items()->strName, $this->items());
  }

  protected function init() {
    parent::init();
    $this->initFilterByParams();
    $filters = [];
    if (($paths = Hook::paths('dd/admin/beforeInitFilters')) !== false) foreach ($paths as $path) include $path;
    $filters = Arr::append($filters, DdGridFilters::getAll($this->getStrName()));
    $this->d['filtersForm'] = (new DdGridFilters($filters, $this->getStrName()))->form->html();
    $this->d['bodyClass'] = 'noOverflow';
  }

  function action_HARD_VIGRUZKA() {
    $longJob = O::gett('DdXls', $this->items()->strName, $this->items());
    $longJob->cycle();
    $this->redirect($longJob->result());
  }

}