<?php

class TestSflmJs extends ProjectTestCase {

  protected function setUp() {
    Sflm::clearCache();
    Sflm::resetFrontend('js', 'dummy');
  }

  function testResetFrontend() {
    Sflm::resetFrontend('js', 'test/dependencies');
    $this->assertTrue(Sflm::frontendName() == 'test/dependencies');
  }

  function testNoAutoloadOnAddingPackage() {
    Sflm::resetFrontend('js', 'test/dependencies');
    $this->assertFalse(Arr::strExists(Sflm::frontend('js')->getPaths(), 'Ngn.sub.A'));
  }

  function testAutoloadOnAddingLib() {
    Sflm::frontend('js')->addLib('test/dependencies');
    $this->assertTrue(Arr::strExists(Sflm::frontend('js')->getPaths(), 'Ngn.sub.A'));
    $this->assertTrue(Arr::strExists(Sflm::frontend('js')->getPaths(), 'Ngn.sub.B'));
  }

  // Ngn.sub, Ngn.sub.A to jsExistingObjects_custom
  function testPathsCaching() {
    Sflm::frontend('js')->addObject('Ngn.sub.A'); // добавляем объект
    Sflm::frontend('js')->store(); //                сторим
    Sflm::resetFrontend('js'); //                    резетим. нельзя получать код больше одного раза во время одного рантайма
    $this->assertTrue(Sflm::frontend('js')->exists('Ngn.sub.A'));
    Sflm::clearCache();
    $this->assertFalse(Sflm::frontend('js')->exists('Ngn.sub.A'));
  }

  function testCodeCaching_sameAfterReset() {
    Sflm::frontend('js')->addObject('Ngn.sub.A'); // добавляем объект
    $code = Sflm::frontend('js')->code(); //         получаем код
    Sflm::frontend('js')->store(); //                сторим
    Sflm::resetFrontend('js'); //                    резетим
    $code2 = Sflm::frontend('js')->code(); //        получаем код
    $this->assertTrue($code == $code2);
  }

  function testCodeCaching_diffAfterAdding() {
    Sflm::frontend('js')->addObject('Ngn.sub.A'); // добавляем объект
    $code = Sflm::frontend('js')->code(); //         получаем код
    Sflm::frontend('js')->addObject('Ngn.sub.B'); // добавляем объект
    $code2 = Sflm::frontend('js')->code(); //        получаем код
    $this->assertTrue($code != $code2);
  }

  function testExistsInExistingObjects() {
    Sflm::frontend('js')->addObject('Ngn.frm');
    Sflm::resetFrontend('js');
    $this->assertTrue(in_array('Ngn.frm', Sflm::frontend('js')->classes->existingObjects));
  }

  function testClearCache() {
    Sflm::frontend('js')->addObject('Ngn.Form.El.Phone');
    Sflm::frontend('js')->store();
    Sflm::clearCache();
    // ни статического ни динамического кэша не существует
    $this->assertFalse((bool)FileCache::c()->load(Sflm::frontend('js')->pathsCacheKey()), 'Dynamic cache must be empty');
    $this->assertFalse(file_exists(Sflm::frontend('js')->cacheFile()), 'Static cache file can not exists. '.Sflm::frontend('js')->cacheFile());
  }

  function testEmptyNewPathsAfterReset() {
    (new FieldEWisiwigSimple2(['name' => 'dummy']))->typeJs();
    Sflm::frontend('js')->getDeltaUrl();
    Sflm::resetFrontend('js');
    $newPaths = Sflm::frontend('js')->newPaths;
    $this->assertFalse((bool)$newPaths, 'New paths must be empty after reset. Current: '.implode(', ', $newPaths));
  }

}