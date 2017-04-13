<?php

class CtrlAdminDdStructure extends CtrlAdmin {

  static $properties = [
    'onMenu' => true,
    'order' => 30
  ];
  
  protected $prepareMainFormTpl = true;
  
  /**
   * @var DdStructuresManager
   */
  protected $im;
  
  protected function init() {
    $this->im = new DdStructuresManager;
  }
  
  function action_default() {
    $this->d['DbItemsExtended'] = $this->im->items->getItems();
  }

  function action_edit() {
    $data = $this->im->items->getItem($this->req->rq('id'));
    $this->setPageTitle('Редактирование структуры «'.$data['title'].'»');
    if ($this->im->requestUpdate($this->req->rq('id'))) {
      $this->redirect($this->tt->getPath(2));
    }
    $this->d['form'] = $this->im->form->html();
  }
  
  function action_new() {
    $this->setPageTitle(Locale::get('structureCreating'));
    if ($this->im->requestCreate()) {
      $this->redirect($this->tt->getPath(2));
    }
    $this->d['form'] = $this->im->form->html();
  }
  
  function action_delete() {
    $this->im->delete($this->req->rq('id'));
    $this->redirect();
  }
  
}

CtrlAdminDdStructure::$properties['title'] = Locale::get('ddStructure');
