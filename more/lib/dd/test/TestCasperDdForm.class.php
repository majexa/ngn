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
    SiteConfig::updateSubVar('userReg', 'enable', true);
    SiteConfig::updateSubVar('userReg', 'phoneConfirm', true);
    SiteConfig::updateSubVar('userReg', 'phoneEnable', true);
    Casper::run(PROJECT_KEY, [
      "default/testDd/dialogForm",
      "default/testUsers/dialogAuth"
    ]);
  }

}