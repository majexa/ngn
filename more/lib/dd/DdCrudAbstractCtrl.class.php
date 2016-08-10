<?php

/**
 * @method void oProcessItems(DdItems $items)
 * @method void oProcessForm(DdForm $form)
 * @method void oProcessDdo(Ddo $ddo)
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

  abstract protected function getStrName();

  /**
   * @param array $options
   * @return DdItems
   */
  protected function items(array $options = []) {
    if (isset($this->items)) return $this->items;
    $this->items = $this->_items($options);
    $this->items->hasPagination = true;
    return $this->objectProcess($this->items, 'items');
  }

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

  /**
   * @var DataManagerAbstract
   */
  protected $im;


  protected function getIm() {
    if (isset($this->im)) return $this->im;
    $this->im = $this->_getIm();
    return $this->im;
  }

  protected function _getIm() {
    $form = new DdForm(new DdFields($this->getStrName()), $this->getStrName());
    return $this->objectProcess(new DdItemsManager($this->items(), $this->objectProcess($form, 'form')), 'im');
  }
  
  abstract protected function id();

  function action_json_new() {
    $im = $this->getIm();
    $im->form->options['submitTitle'] = Locale::get('create');
    $im->form->action = $this->req->options['uri'];
    if (($id = $im->requestCreate())) return $id;
    $this->jsonFormAction($im->form);
    return false;
  }

  function action_json_edit() {
    $im = $this->getIm();
    $im->form->action = $this->req->options['uri']; // TODO xss
    if ($im->requestUpdate($this->id())) return true;
    $this->jsonFormAction($im->form);
    return false;
  }

  function action_json_getItems() {
    $this->json = $this->getGrid();
    if ($this->items()->hasPagination) $this->json['pagination'] = $this->items()->getPagination();
  }

  function action_json_search() {
    $this->items()->addSearchFilter('%'.$this->req->rq('word').'%');
    $this->action_json_getItems();
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