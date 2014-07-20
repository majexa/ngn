<?php

class TestSflmJs extends ProjectTestCase {

  protected function setUp() {
    Sflm::clearCache();
    Sflm::setFrontend('js', 'admin');
  }

  function testClearCache() {
    Sflm::frontend('js')->addObject('Ngn.Form');
    Sflm::frontend('js')->addObject('Ngn.Form.El.Phone');
    Sflm::frontend('js')->store();
    Sflm::clearCache();
    // ни статического ни динамического кэша не существует
    $this->assertFalse((bool)SflmCache::c()->load(Sflm::frontend('js')->pathsCacheKey()), 'Dynamic cache must be empty');
    $this->assertFalse(file_exists(Sflm::frontend('js')->cacheFile()), 'Static cache file can not exists. '.Sflm::frontend('js')->cacheFile());
  }

  function testParentNamespaceInitInTheSameFile() {
    Sflm::frontend('js')->addObject('Ngn.namespace.A');
  }

  //function testChangeVersion() {
  //  Sflm::frontend('js')->addObject('Ngn.sub.A');
  //  $version1 = Sflm::frontend('js')->version();
  //  Sflm::setFrontend('js', 'dummy');
  //  $version2 = Sflm::frontend('js')->version();
  //  Sflm::frontend('js')->addObject('Ngn.sub.B');
  //  Sflm::frontend('js')->version();
  //  $version3 = Sflm::frontend('js')->version();
  //}

  function testResetFrontend() {
    Sflm::setFrontend('js', 'test/dependencies');
    $this->assertTrue(Sflm::frontendName() == 'test/dependencies');
  }

  function testNoAutoloadOnAddingPackage() {
    Sflm::setFrontend('js', 'test/dependencies');
    $this->assertFalse(Arr::strExists(Sflm::frontend('js')->getPaths(), 'Ngn.sub.A'));
  }

  function testObjectInPaths() {
    $this->assertTrue(isset(Sflm::frontend('js')->classes->objectPaths['Ngn.sub.A']));
    $this->assertFalse(isset(Sflm::frontend('js')->classes->objectPaths['Ngn.Outside']));
  }

  function testAutoloadOnAddingLib() {
    Sflm::frontend('js')->addLib('test/dependencies');
    $this->assertTrue(Arr::strExists(Sflm::frontend('js')->getPaths(), 'Ngn.sub.A'));
    $this->assertTrue(Arr::strExists(Sflm::frontend('js')->getPaths(), 'Ngn.sub.B'));
  }

  function testCodeCaching_sameAfterReset() {
    Sflm::frontend('js')->addObject('Ngn.sub.A'); // добавляем объект
    $code = Sflm::frontend('js')->code(); //         получаем код
    Sflm::frontend('js')->store(); //                сторим
    Sflm::setFrontend('js'); //                    резетим
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
    Sflm::setFrontend('js');
    $this->assertTrue(in_array('Ngn.frm', Sflm::frontend('js')->classes->existingObjects));
  }

  // Ngn.sub, Ngn.sub.A to jsExistingObjects_custom
  function testPathsCaching() {
    Sflm::frontend('js')->addObject('Ngn.sub.A'); // добавляем объект
    Sflm::frontend('js')->store(); //                сторим
    Sflm::setFrontend('js'); //                    резетим. нельзя получать код больше одного раза во время одного рантайма
    $this->assertTrue(Sflm::frontend('js')->exists('Ngn.sub.A'));
    Sflm::clearCache();
    $this->assertFalse(Sflm::frontend('js')->exists('Ngn.sub.A'));
  }

  function testEmptyNewPathsAfterReset() {
    (new FieldEWisiwigSimpleLinks(['name' => 'dummy']))->typeJs();
    Sflm::frontend('js')->getDeltaUrl();
    Sflm::setFrontend('js');
    $newPaths = Sflm::frontend('js')->newPaths;
    $this->assertFalse((bool)$newPaths, 'New paths must be empty after reset. Current: '.implode(', ', $newPaths));
  }

}