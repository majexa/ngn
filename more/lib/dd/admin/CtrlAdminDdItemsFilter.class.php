<?php

class CtrlAdminDdItemsFilter extends CtrlAdminDdItems {
  use DdParamFilterCtrl/*, LongJobCtrl*/;

  protected function _paramFilterItems() {
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
    return O::di('DdXls', $this->items()->strName, $this->items());
  }

  function action_default() {
    parent::action_default();
    $filters = [];
    foreach (Hook::paths('dd/admin/beforeInitFilters') as $path) include $path;
    $filters = Arr::append($filters, DdGridFilters::getAll($this->getStrName()));
    $this->d['filtersForm'] = (new DdGridFilters($filters, $this->getStrName()))->form->html();
    $this->d['bodyClass'] = 'noOverflow';
  }

  function action_json_getItems() {
    $this->initFilterByParams();
    parent::action_json_getItems();
  }

//  function action_HARD_VIGRUZKA() {
//    $longJob = O::di('DdXls', $this->items()->strName, $this->items());
//    $longJob->cycle();
//    $this->redirect($longJob->result());
//  }

}