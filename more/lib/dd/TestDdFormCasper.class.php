<?php

class TestDdFormCasper extends TestDdForm {

  static function setUpBeforeClass() {
    (new DdFieldsManager('a'))->create([
      'title' => 'Title',
      'name' => 'title',
      'type' => 'text'
    ]);
  }

  function test() {
  }

}