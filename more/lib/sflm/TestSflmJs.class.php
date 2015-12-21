<?php

class TestSflmJs extends ProjectTestCase {

  protected function setUp() {
    Sflm::clearCache();
    Sflm::setFrontend('js', 'default');
  }

  function testAbsPathExistsAfterReset() {
    Sflm::frontend('js')->addFile(NGN_PATH.'/i/js/ngn/test/Ngn.Sub.js');
    Sflm::frontend('js')->store();
    Sflm::setFrontend('js');
    Arr::strExists(Sflm::frontend('js')->getAbsPathsCache(), '/i/js/ngn/test/Ngn.Sub.js');
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
    (new FieldEWisiwigSimpleLinks(['name' => 'dummy']))->typeCssAndJs();
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

  function testDebugPathTagsPresentsInResultHtml() {
    $this->_testDebugPathRendersAsSeparateHtmlTags('Ngn.Sub');
  }

  protected function _testDebugPathRendersAsSeparateHtmlTags($debugPath) {
    Sflm::$debugUrl = 'abc';
    Sflm::$debugPaths = [
      'js' => [
        $debugPath
      ]
    ];
    Sflm::clearCache();
    Sflm::frontend('js')->addPath('i/js/ngn/test/Ngn.Sub.B.js');
    Sflm::frontend('js')->store();
    $tags = <<<TAGS
<script src="abc/i/js/ngn/test/Ngn.Sub.js" type="text/javascript"></script>
<script src="abc/i/js/ngn/test/Ngn.Sub.A.js" type="text/javascript"></script>
<script src="abc/i/js/ngn/test/Ngn.Sub.B.js" type="text/javascript"></script>
TAGS;
    $this->assertTrue((bool)strstr(Sflm::frontend('js')->getTagsDebug(), $tags));
  }

  function testDebugPathOnAddingClass() {
    // добавляем часть пути класса в список отладочных путей
    Sflm::$debugPaths = [
      'js' => [
        'test/Ngn.Sub.B.js'
      ]
    ];
    // добавляем класс
    Sflm::frontend('js')->addClass('Ngn.Sub.B');
    // получаем теги
    Sflm::frontend('js')->store();
    Sflm::frontend('js')->getTagsDebug();
    Sflm::setFrontend('js');
    // добавляем класс, но он уже в кэше. до добавления пути, где проверяются отладочные пути не доходит
    Sflm::frontend('js')->addClass('Ngn.Sub.B');
    Sflm::frontend('js')->store();
    // тег есть
    $this->assertTrue((bool)strstr(Sflm::frontend('js')->getTagsDebug(), 'Ngn.Sub.B'));
    // класса не должно быть в основном файле
    $this->assertFalse((bool)strstr(Sflm::frontend('js')->_code(), 'Ngn.Sub.B'));
    // зато там должен быть Ngn.Sub.A от которого наследуется Ngn.Sub.B и которого нет в отладочных путях
    $this->assertTrue((bool)strstr(Sflm::frontend('js')->_code(), 'Ngn.Sub.A'));
  }

  function testImplementsExtendsAddsBefore() {
    Sflm::frontend('js')->addClass('Ngn.B1');
    Sflm::frontend('js')->store();
    $c = file_get_contents(Sflm::$webPath.'/js/cache/default.js');
    $this->assertTrue(strpos($c, 'Ngn.B1') > strpos($c, 'Ngn.C1.Ooo'), '"Extends: ..." pattern error');
    $this->assertTrue(strpos($c, 'Ngn.B1') > strpos($c, 'Ngn.A1.Ooo'), '"Implements: ..." pattern error');
    $this->assertTrue(strpos($c, 'Ngn.B1') > strpos($c, 'Ngn.D1'), '"Implements: ..." pattern error');
    $this->assertTrue(strpos($c, 'Ngn.B1') > strpos($c, 'Ngn.E1'), '"e1: ..." pattern error');
  }

  function testParseClassFunctionUsage() {
    $classes = SflmJsClasses::parseValidClassesUsage('Ngn.A1.aaa.bbb');
    $this->assertTrue($classes[0] == 'Ngn.A1');
  }

}