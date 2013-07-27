<?php

class DdSlaveItemsPage extends DdItemsPage {
  
  private $masterStrName;
  
  private $masterPageId;
  
  function __construct($pageId, $masterStrName, $masterPageId) {
    parent::__construct($pageId);
    $this->masterStrName = $masterStrName;
    $this->masterPageId = $masterPageId;
    if (!isset($this->masterStrName))
      throw new Exception('$this->masterStrName not defined');
    if (!isset($this->masterPageId))
      throw new Exception('$this->masterPageId not defined');
  }
  
  function create(array $data) {
    $id = parent::create($data);
    $this->clearMasterCache($id);
    return $id;
  }
  
  function update($id, array $data) {
    // необходимо в случае переноса записи в другой master-раздел
    $this->clearMasterCache($id);
    parent::update($id, $data);
    $this->clearMasterCache($id);
  }
  
  function activate($id) {
    parent::activate($id);
    $this->clearMasterCache($id);
  }
  
  function deactivate($id) {
    parent::deactivate($id);
    $this->clearMasterCache($id);
  }
  
  function delete($id) {
    $this->clearMasterCache($id);
    parent::delete($id);
  }
  
  protected function clearMasterCache($id) {
    $item = $this->getItem($id);
    $this->_clearMasterCache($item[DdCore::masterFieldName]);
  }

  protected function _clearMasterCache($masterItemId) {
    DdItemsCacher::cc($this->masterStrName, $masterItemId);
    PageModuleCore::action($this->page['module'], 'clearMasterCache', [
      'masterStrName' => $this->masterStrName,
      'masterItemId' => $masterItemId
    ]);
  }
  
  protected function extendItem(array &$item) {
    $item[DdCore::masterFieldName] = O::get('DdItemsPage', $this->masterPageId)->
      getItem($item[DdCore::masterFieldName]);
  }

  protected function extendItems(array &$items) {
    foreach ($items as &$item)
      $this->extendItem($item);
  }
  
}