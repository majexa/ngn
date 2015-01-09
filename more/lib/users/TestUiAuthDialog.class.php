<?php

class TestUiAuthDialog extends ProjectTestCase {

  function test() {
    SiteConfig::replaceConstant('more', 'TESTING', true);
    SiteConfig::updateSubVar('userReg', 'emailEnable', false);
    SiteConfig::updateSubVar('userReg', 'enable', true);
    SiteConfig::updateSubVar('userReg', 'phoneEnable', true);
    SiteConfig::updateSubVar('userReg', 'phoneConfirm', true);
    Casper::runFile('userRegPhone');
  }

}