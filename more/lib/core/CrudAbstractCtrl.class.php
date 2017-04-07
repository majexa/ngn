<?php

/**
 * @method void oProcessItems(DdItems $items)
 * @method void oProcessForm(DdForm $form)
 */
trait CrudAbstractCtrl {
  use ObjectProcessorCtrl;

  /**
   * @var DbItems
   */
  protected $items;

  /**
   * Возвращает ID для работы с конкретной записью, полученный из http-запроса
   *
   * @return string
   */
  abstract protected function id();

  /**
   * @param array $options
   * @return UpdatableItems
   */
  abstract protected function items(array $options = []);

  /**
   * @return GridData
   */
  abstract protected function getGrid();

//  /**
//   * @var DataManagerAbstract
//   */
//  protected $im;

//  protected function getIm() {
//    if (isset($this->im)) return $this->im;
//    $this->im = $this->_getIm();
//    return $this->im;
//  }
//
//  abstract protected function _getIm();

  function action_json_new() {
    $im = $this->getIm();
    $im->form->options['submitTitle'] = Locale::get('create');
    $im->form->action = $this->req->options['uri'];
    if (($id = $im->requestCreate())) return $id;
    $this->jsonFormAction($im->form);
    return false;
  }

//  function action_json_edit() {
//    $im = $this->getIm();
//    $im->form->action = $this->req->options['uri']; // TODO xss
//    if ($im->requestUpdate($this->id())) return true;
//    $this->jsonFormAction($im->form);
//    return false;
//  }

  function action_json_default() {
    $this->action_json_getItems();
  }

  function action_json_getItem() {
    $this->json = $this->items()->getItem($this->id());
  }

  function action_json_getItems() {
    $grid = $this->getGrid();
    $this->json = is_object($grid) ? $grid->r : $grid;
    if (isset($this->items()->hasPagination)) $this->json['pagination'] = $this->items()->getPagination();
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

