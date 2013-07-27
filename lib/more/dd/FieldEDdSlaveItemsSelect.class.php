<?php

DdFieldCore::registerType('ddSlaveItemsSelect', [
  'dbType' => 'INT',
  'dbLength' => 11,
  'title' => 'Выбор мастер dd-записи',
  'order' => 300
]);

class FieldEDdSlaveItemsSelect extends FieldESelect {

  protected function allowedFormClass() {
    return 'DdFormPageSlave';
  }

  protected function init() {
    $this->options['options'] = ['' => '—'];
    $oI = new DdItemsPage($this->oForm->masterPageId);
    foreach ($oI->getItems() as $id => $v)
      $this->options['options'][$id] = $v['title'];
    parent::init();
  }

}