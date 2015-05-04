<?php

abstract class TestFieldDd extends TestDd {

  /**
   * @var DdItemsManager
   */
  static $im;

  static function setUpBeforeClass() {
    parent::setUpBeforeClass();
    $fieldType = lcfirst( //
      Misc::removePrefix('TestUiField', //
        Misc::removePrefix('TestField', //
          get_called_class())));
    O::di('DdFieldsManager', static::$strName)->create(static::fieldData($fieldType));
    O::di('DdFieldsManager', static::$strName)->create([
      'name'  => 'sample2',
      'title' => 'sample2',
      'type'  => 'file'
    ]);
    self::$im = DdCore::imDefault(static::$strName);
  }

  static protected function fieldData($fieldType) {
    return [
      'name'  => 'sample',
      'title' => 'sample',
      'type'  => $fieldType
    ];
  }

  abstract function createData();

  protected $itemId;

  function createItem($request = false) {
    if ($request) {
      static::$im->form->req->r['formId'] = static::$im->form->getAllData()['formId'];
      static::$im->form->req->p = $this->createData();
      $this->itemId = static::$im->requestCreate();
    } else {
      $this->itemId = static::$im->create($this->createData());
    }
  }

  function updateItem($data, $request = false) {
    if ($request) {
      static::$im->form->req->r['formId'] = static::$im->form->getAllData()['formId'];
      static::$im->form->req->p = $this->createData();
      static::$im->requestUpdate($this->itemId);
    } else {
      static::$im->update($this->itemId, $data);
    }
  }

  function fillForm() {
    unset(static::$im->form->req->r['formId']);
    static::$im->requestUpdate($this->itemId);
  }

  abstract function runTests($request = false);

  function test() {
    $this->createItem();
    $this->runTests();
  }

}