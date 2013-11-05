<?php

class TestDdAllFields extends TestDd {

  function testCreate() {
    /* @var $fm DdFieldsManager */
    $fm = O::gett('DdFieldsManager', 'a');
    foreach (DdFieldCore::getTypes() as $name => $v) {
      $id = $fm->create([
        'name' => $name.'1',
        'title' => $v['title'],
        'type' => $name
      ]);
      $this->assertTrue((bool)$id);
    }
  }

  static function tearDownAfterClass() {}

  function testUpdate() {
    DdItemsManager::getDefault('a')->form->setElementsData()->html();
  }

  /*
  function testCreateData() {
  }
  */

}