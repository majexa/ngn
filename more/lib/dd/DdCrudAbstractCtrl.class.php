<?php

/**
 * @method void oProcessDdo(Ddo $ddo)
 */
trait DdCrudAbstractCtrl {
  use CrudAbstractCtrl;

  /**
   * Имя структуры для
   *
   * @return string
   */
  abstract protected function getStrName();

  protected function getDdLayout() {
    return 'siteItems';
  }

  protected function ddo() {
    return $this->objectProcess(new Ddo($this->getStrName(), $this->getDdLayout()), 'ddo');
  }

  protected function ddoEdit() {
    return $this->objectProcess(new DdoAdmin($this->getStrName(), 'adminItems'), 'ddo');
  }

  protected function addGridSfl() {
    Sflm::frontend('css')->addLib('interface');
  }

  protected function getGrid() {
    $this->addGridSfl();
    return Ddo::getGrid($this->items()->getItems(), $this->ddoEdit());
  }

  protected function _getIm() {
    $form = new DdForm(new DdFields($this->getStrName()), $this->getStrName());
    return $this->objectProcess(new DdItemsManager($this->items(), $this->objectProcess($form, 'form')), 'im');
  }

  function action_json_search() {
    $this->items()->cond->addSearchFilter('%'.$this->req->rq('word').'%');
    $this->action_json_getItems();
  }

  function action_ajax_activate() {
    $this->items()->activate($this->id());
  }

  function action_ajax_deactivate() {
    $this->items()->deactivate($this->id());
  }

  function action_ajax_reorder() {
    $this->items()->reorderItems($this->req->rq('ids'));
  }

}