<?php

class AdminDdItemsSettingsForm extends Form {

  protected $structureId;

  function __construct($structureId) {
    $this->structureId = $structureId;
    parent::__construct([
      [
        'title' => 'Включить ручную сортировку',
        'name'  => 'enableManualOrder',
        'type'  => 'bool'
      ],
      [
        'title' => 'Показывать отключенные записи',
        'name'  => 'getNonActive',
        'type'  => 'bool'
      ],
    ]);
  }

  protected function init() {
    $this->setElementsData((new DdStructureItems)->getItem($this->structureId)['settings'] ?: []);
  }

  function _update(array $data) {
    (new DdStructureItems)->updateField($this->structureId, 'settings', $data);
  }

}

class CtrlAdminDdItems extends CtrlAdmin {
  use DdCrudCtrl;

  protected $structure;

  protected function init() {
    $this->structure = (new DdStructureItems)->getItemByField('name', $this->getStrName());
    Sflm::frontend('js')->addObject('Ngn.DdGrid.Admin');
  }

  protected function _getIm() {
    return new DdItemsManager($this->items(), $this->objectProcess(new DdForm(new DdFields($this->getStrName(), ['getDisallowed' => true]), $this->getStrName()), 'form'));
  }

  protected function getParamActionN() {
    return 3;
  }

  protected function getStrName() {
    return $this->req->param(2);
  }

  function action_default() {
    $this->d['settings'] = $this->structure['settings'];
    $this->req->param(2); // required
  }

  function action_form() {
    $this->d['tpl'] = 'common/form';
    $this->d['form'] = DdCore::imDefault($this->getStrName())->form->setElementsData()->html();
  }

  function action_json_settings() {
    $this->json['title'] = 'Настройки записей';
    return $this->jsonFormActionUpdate(new AdminDdItemsSettingsForm($this->structure['id']));
  }

  function processForm(DdForm $form) {
    $form->options['deleteFileUrl'] = $this->tt->getPath(2).'/'.$this->req->param(2).'/deleteFile?id='.$this->req['id'];
  }

  function processItemsAdmin(DdItems $items) {
    $items->getNonActive = true;
  }

}