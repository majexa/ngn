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
