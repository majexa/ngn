<?php

/**
 * @method void processItems(DdItems $items)
 * @method void processForm(DdForm $form)
 * @method void processDdo(Ddo $ddo)
 */
trait DdCrudAbstractCtrl {
use ObjectProcessorCtrl;

  /**
   * @var DdItems
   */
  protected $items;

  /**
   * @abstract
   * @return DdItems
   */
  abstract protected function _items();

  protected function getStrName() {
    return lcfirst(Misc::removePrefix('Ctrl', get_class($this)));
  }

  /**
   * @return DdItems
   */
  protected function items() {
    if (isset($this->items)) return $this->items;
    $this->items = $this->_items();
    $this->items->isPagination = true;
    return $this->objectProcess($this->items, 'items');
  }

  protected function getDdLayout() {
    return 'siteItems';
    return Misc::isAdmin() ? 'adminItems' : 'siteItems';
  }

  protected function ddo() {
    return $this->objectProcess(new Ddo($this->getStrName(), $this->getDdLayout()), 'ddo');
  }

  protected function ddoEdit() {
    return $this->objectProcess(new DdoAdmin($this->getStrName(), $this->getDdLayout()), 'ddo');
  }

  protected function getGrid() {
    return Ddo::getGrid($this->items()->getItems(), $this->ddoEdit());
  }

  protected $im;

  protected function getIm() {
    if (isset($this->im)) return $this->im;
    $this->im = $this->_getIm();
    return $this->im;
  }

  protected function _getIm() {
    return new DdItemsManager($this->items(), $this->objectProcess(new DdForm(new DdFields($this->getStrName()), $this->getStrName()), 'form'));
  }
  
  protected function id() {
    return $this->id();
  }

  function action_json_new() {
    $im = $this->getIm();
    $im->form->options['submitTitle'] = 'Создать';
    if (($id = $im->requestCreate())) return $id;
    $this->jsonFormAction($im->form);
    return false;
  }

  function action_json_edit() {
    $im = $this->getIm();
    if ($im->requestUpdate($this->id())) return true;
    $this->jsonFormAction($im->form);
    return false;
  }

  function action_json_getItems() {
    $this->json = $this->getGrid();
    if ($this->items()->isPagination) $this->json['pagination'] = $this->items()->getPagination();
  }

  function action_ajax_delete() {
    $this->getIm()->delete($this->id());
  }

  function action_ajax_activate() {
    $this->items()->activate($this->id());
  }

  function action_ajax_deactivate() {
    $this->items()->deactivate($this->id());
  }

  function action_ajax_changeState() {
    $this->getIm()->updateField($this->id(), $this->req['field'], (bool)$this->req['state']);
  }

  function action_ajax_updateField() {
    $this->getIm()->updateField($this->id(), $this->req['field'], $this->req['value']);
  }

  function action_ajax_reorder() {
    $this->items()->reorderItems($this->req->rq('ids'));
  }

  function action_ajax_deleteFile() {
    $this->deleteFile();
  }

  function action_deleteFile() {
    $this->deleteFile();
    redirect('/');
  }

  function deleteFile() {
    $this->getIm()->deleteFile($this->id(), $this->req->rq('fieldName'));
  }

}