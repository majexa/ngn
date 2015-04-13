<?php

class UestCasperDdForm extends TestDd {

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
    ProjectConfig::updateSubVar('userReg', 'enable', true);
    ProjectConfig::updateSubVar('userReg', 'phoneConfirm', true);
    ProjectConfig::updateSubVar('userReg', 'phoneEnable', true);
    Casper::run(PROJECT_KEY, [
      "default/testDd/dialogForm",
      "default/testUsers/dialogAuth"
    ]);
  }

}