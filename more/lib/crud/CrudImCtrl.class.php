<?php

trait CrudImCtrl {
  use CrudAbstractCtrl;

  /**
   * @var DataManagerAbstract
   */
  protected $im;

  protected function getIm() {
    if (isset($this->im)) return $this->im;
    $this->im = $this->_getIm();
    return $this->im;
  }

  abstract protected function _getIm();

  function action_ajax_delete() {
    $this->getIm()->delete($this->id());
  }

  function action_json_new() {
    $im = $this->getIm();
    $this->json['title'] = $im->form->options['submitTitle'] = Locale::get('create');
    $im->form->action = $this->req->options['uri'];
    if (($id = $im->requestCreate())) return $id;
    $this->jsonFormAction($im->form);
    return false;
  }

  function action_json_edit() {
    $im = $this->getIm();
    $im->form->action = htmlspecialchars($this->req->options['uri']);
    if ($im->requestUpdate($this->id())) return true;
    $this->jsonFormAction($im->form);
    return false;
  }

  function action_ajax_updateField() {
    $this->getIm()->updateField($this->id(), $this->req['field'], $this->req['value']);
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