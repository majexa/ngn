<?php

/**
 * @method void processItems(DdItems $items)
 * @method void processForm(DdForm $form)
 * @method void processDdo(Ddo $ddo)
 */
trait DdCrudAbstractCtrl {
use ObjectProcessorCtrl;

  /**
   * @abstract
   * @return DdItems
   */
  abstract protected function items();

  protected function getStrName() {
    return lcfirst(Misc::removePrefix('Ctrl', get_class($this)));
  }

  /**
   * @var DdItems
   */
  protected $items;

  /**
   * @return DdItems
   */
  protected function getItems() {
    if (isset($this->items)) return $this->items;
    $this->items = $this->items();
    $this->items->isPagination = true;
    return $this->objectProcess($this->items, 'items');
  }

  protected function getDdLayout() {
    return Misc::isAdmin() ? 'adminItems' : 'siteItems';
  }

  protected function ddo() {
    return $this->objectProcess(new DdoAdmin($this->getStrName(), $this->getDdLayout()), 'ddo');
  }

  protected function getGrid() {
    return Ddo::getGrid($this->getItems()->getItems_cache(), $this->ddo());
  }

  protected $im;

  protected function getIm() {
    if (isset($this->im)) return $this->im;
    $this->im = $this->_getIm();
    ;
    return $this->im;
  }

  protected function _getIm() {
    return new DdItemsManager($this->items(), $this->objectProcess(new DdForm(new DdFields($this->getStrName()), $this->getStrName()), 'form'));
  }

  function action_json_new() {
    $im = $this->getIm();
    $im->form->options['submitTitle'] = 'Создать';
    if ($im->requestCreate()) return;
    $this->jsonFormAction($im->form);
  }

  function action_json_edit() {
    $im = $this->getIm();
    if ($im->requestUpdate($this->req['id'])) return;
    $this->jsonFormAction($im->form);
  }

  function action_json_getItems() {
    $this->json = $this->getGrid();
    if ($this->getItems()->isPagination) $this->json['pagination'] = $this->getItems()->getPagination();
  }

  function action_ajax_delete() {
    $this->getIm()->delete($this->req['id']);
  }

  function action_ajax_activate() {
    $this->items()->activate($this->req['id']);
  }

  function action_ajax_deactivate() {
    $this->items()->deactivate($this->req['id']);
  }

  function action_ajax_changeState() {
    $this->getIm()->updateField($this->req['id'], $this->req['field'], (bool)$this->req['state']);
  }

  function action_ajax_updateField() {
    $this->getIm()->updateField($this->req['id'], $this->req['field'], $this->req['value']);
  }

  function action_ajax_reorder() {
    $this->getItems()->reorderItems($this->req->rq('ids'));
  }

  function action_ajax_deleteFile() {
    $this->deleteFile();
  }

  function action_deleteFile() {
    $this->deleteFile();
    redirect('/');
  }

  function deleteFile() {
    $this->getIm()->deleteFile($this->req['id'], $this->req->rq('fieldName'));
  }

}