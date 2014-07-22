<?php

class TestCasperDdForm extends TestDd {

  static function setUpBeforeClass() {
    parent::setUpBeforeClass();
    (new DdFieldsManager('a'))->create([
      'title' => 'Title',
      'name' => 'title',
      'type' => 'text'
    ]);
  }

  function test() {
    print `pm localProject cc test`;
    TestRunnerCasper::runTest(PROJECT_KEY, [
      "default/testDd/dialogForm",
      "default/testUsers/dialogAuth"
    ]);
  }

}