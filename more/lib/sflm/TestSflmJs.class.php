<?php

class TestSflmJs extends ProjectTestCase {

  protected function setUp() {
    Sflm::clearCache();
    Sflm::setFrontend('js', 'default');
  }

  function testStripComments() {
    $after = Sflm::stripComments("a
/* ... */
// ...
// @req
b");
    $this->assertTrue($after == "a

// @req
b");
  }

  function testValidClassesParsing() {
    $r = SflmJsClasses::parseValidClasses("
Ngn.Aaa
Ngn.Aaa.Aaa
Ngn.aaa.Aaa
Ngn.aaa.aaa.aaa.Aaa
Ngn.Aaa.Aaa.Aaa.aaa
'Ngn.aaa.aaa.'
Ngn.Aaa.Aaa.aaa
Ngn.Aaa.aaa
Ngn.aaa
");
    $this->assertTrue($r[3] == 'Ngn.aaa.aaa.aaa.Aaa');
    $this->assertTrue(count($r) === 4);
  }

  function testClearCache() {
    Sflm::frontend('js')->addClass('Ngn.Form');
    Sflm::frontend('js')->addClass('Ngn.Form.El.Phone');
    Sflm::frontend('js')->store();
    Sflm::clearCache();
    // ни статического ни динамического кэша не существует
    $this->assertFalse((bool)SflmCache::c()->load(Sflm::frontend('js')->pathsCacheKey()), 'Dynamic cache must be empty');
    $this->assertFalse(file_exists(Sflm::frontend('js')->cacheFile()), 'Static cache file can not exists. '.Sflm::frontend('js')->cacheFile());
  }

  function testParentNamespaceInitInTheSameFile() {
    Sflm::frontend('js')->addClass('Ngn.namespace.A');
  }

  function testResetFrontend() {
    Sflm::setFrontend('js', 'test/dependencies');
    $this->assertTrue(Sflm::frontendName() == 'test/dependencies');
  }

  function testNoAutoloadOnAddingPackage() {
    Sflm::setFrontend('js', 'test/dependencies');
    $this->assertFalse(Arr::strExists(Sflm::frontend('js')->getPaths(), 'Ngn.Sub.A'));
  }

  //function testAutoloadOnAddingLib() {
  // not supported anymore
  //  Sflm::frontend('js')->addLib('test/dependencies');
  //  $this->assertTrue(Arr::strExists(Sflm::frontend('js')->getPaths(), 'Ngn.Sub.A'));
  //  $this->assertTrue(Arr::strExists(Sflm::frontend('js')->getPaths(), 'Ngn.Sub.B'));
  //}

  function testCodeCaching_sameAfterReset() {
    Sflm::frontend('js')->addClass('Ngn.Sub.A'); // добавляем объект
    $code = Sflm::frontend('js')->code(); //        получаем код
    Sflm::frontend('js')->store(); //               сторим
    Sflm::setFrontend('js'); //                     резетим
    $code2 = Sflm::frontend('js')->code(); //       получаем код
    $this->assertTrue($code == $code2);
  }

  function testCodeCaching_diffAfterAdding() {
    Sflm::frontend('js')->addClass('Ngn.Sub.A'); // добавляем объект
    $code = Sflm::frontend('js')->code(); //        получаем код
    Sflm::frontend('js')->addClass('Ngn.Sub.B'); // добавляем объект
    $code2 = Sflm::frontend('js')->code(); //       получаем код
    $this->assertTrue($code != $code2);
  }

  function testClassExistsAfterReset() {
    Sflm::frontend('js')->addClass('Ngn.Sub');
    Sflm::frontend('js')->store();
    Sflm::setFrontend('js');
    $this->assertTrue(Sflm::frontend('js')->classes->frontendClasses->exists('Ngn.Sub'));
    $this->assertTrue(Arr::strExists(Sflm::frontend('js')->getPaths(), 'Ngn.Sub.js'));
  }

  function testEmptyNewPathsAfterReset() {
    (new FieldEWisiwigSimpleLinks(['name' => 'dummy']))->typeJs();
    Sflm::frontend('js')->store();
    Sflm::frontend('js')->getDeltaUrl();
    Sflm::setFrontend('js');
    $newPaths = Sflm::frontend('js')->newPaths;
    $this->assertFalse((bool)$newPaths, 'New paths must be empty after reset. Current: '.implode(', ', $newPaths));
  }

  function testExtending4levelNamespace() {
    Sflm::frontend('js')->addClass('Ngn.Sub.A.Cb');
    $this->assertTrue(Sflm::frontend('js')->classes->frontendClasses->exists('Ngn.Sub.A.Bb'));
  }

  function testPreloadingInTheSameFile() {
    set_time_limit(1);
    Sflm::frontend('js')->addClass('Ngn.Preload');
  }

}