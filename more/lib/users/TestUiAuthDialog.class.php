<?php

class TestUiAuthDialog extends ProjectTestCase {

  function test() {
    SiteConfig::updateSubVar('userReg', 'enable', true);
    SiteConfig::updateSubVar('userReg', 'phoneEnable', true);
    SiteConfig::updateSubVar('userReg', 'phoneConfirm', true);
    Casper::run(PROJECT_KEY, [
      ['~thenUrl', 'default/testUsers/dialogAuth'],
      ['~click', '.tab-menu li:nth-child(2) a'],
      ['~fill', 'form#formReg', [
        'phone' => '+79202560776'
      ]],
      ['~click', '#formReg .name_send a.btn']
    ]);
  }

}