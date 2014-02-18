<?php

abstract class TestFieldAbstract extends TestDd {

  /**
   * @var DdItemsManager
   */
  static $im;

  /**
   * @var DdFieldsManager
   */
  static $fm;

  static $fieldId;

  static function setUpBeforeClass() {
    parent::setUpBeforeClass();
    self::$fm = O::gett('DdFieldsManager', 'a');
    $fieldType = lcfirst(Misc::removePrefix('TestField', get_called_class()));
    self::$fieldId = self::$fm->create(static::fieldData($fieldType));
    self::$im = DdCore::imDefault('a');
  }

  static protected function fieldData($fieldType) {
    return [
      'name'  => 'sample',
      'title' => 'sample',
      'type'  => $fieldType
    ];
  }

  abstract function createData();

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