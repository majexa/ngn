<?php

class CtrlAdminDdo extends CtrlAdmin {

  static $properties = [
    'title' => 'Управление выводом полей',
    'order' => 30
  ];

  protected $strName;

  protected $moduleName;

  protected $layouts;

  protected $curLayoutName;

  /**
   * @var DdoSettings
   */
  public $settings;

  protected function init() {
    $this->strName = $this->req->param(2);
    $this->settings = new DdoSettings($this->strName);
    $this->layouts = $this->settings->getLayouts();
    if (isset($this->req->params[3]) and isset($this->layouts[$this->req->params[3]])) $this->d['curLayoutName'] = $this->curLayoutName = $this->req->params[3];
    else
      $this->d['curLayoutName'] = $this->curLayoutName = Arr::first_key($this->layouts);
  }

  function action_default() {
    $str = O::get('DbItems', 'dd_structures')->getItemByField('name', $this->strName);
    //$this->setPageTitle('Управление выводом полей структуры «<b>'.$str['title'].'</b>» модуля «<b>'.PageModuleCore::getTitle($this->page).'</b>»');
    $this->d['settings'] = $this->settings;
    $this->d['show'] = $this->settings->getShowAll();
    $this->d['titled'] = $this->settings->getDataAll('titled');
    $this->d['outputMethod'] = $this->settings->getOutputMethod($this->curLayoutName);
    $this->d['layouts'] = $this->layouts;
    $this->d['fields'] = (new DdoFields(new DdoSettings($this->strName), $this->curLayoutName, $this->strName, [
      'getAll'       => true,
      'forceAllowed' => true
    ]))->getFields();
  }

  function action_json_updateFieldsOutputSettings() {
    $this->settings->updateShow(isset($this->req->p['show']) ? $this->req->p['show'] : []);
    $this->settings->updateOutputMethod(Arr::filterEmptiesR($this->req->p['outputMethod']));
    $this->settings->updateTitled(isset($this->req->p['titled']) ? $this->req->p['titled'] : []);
  }

  function action_ajax_reorder() {
    $n = 0;
    foreach ($this->req->rq('ids') as $fieldName) {
      $n += 10;
      $oids[$fieldName] = $n;
    }
    $this->settings->updateOrderIds($oids, $this->curLayoutName);
  }

}