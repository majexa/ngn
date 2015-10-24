<?php

class TestSflmDebugPaths extends ProjectTestCase {

  protected function setUp() {
    Sflm::clearCache();
    Sflm::setFrontend('js', 'default');
  }

/*  function testDebugPathOnAddingClass() {
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
    Sflm::frontend('js')->getTags();
    Sflm::setFrontend('js');
    // добавляем класс, но он уже в кэше. до добавления пути, где проверяются отладочные пути не доходит
    Sflm::frontend('js')->addClass('Ngn.Sub.B');
    Sflm::frontend('js')->store();
    // тег есть
    $this->assertTrue((bool)strstr(Sflm::frontend('js')->getTags(), 'Ngn.Sub.B'));
    // класса не должно быть в основном файле
    $this->assertFalse((bool)strstr(Sflm::frontend('js')->_code(), 'Ngn.Sub.B'));
    // зато там должен быть Ngn.Sub.A от которого наследуется Ngn.Sub.B и которого нет в отладочных путях
    $this->assertTrue((bool)strstr(Sflm::frontend('js')->_code(), 'Ngn.Sub.A'));
  }*/

  function test() {
    Sflm::$debugPaths = [
      'js' => [
        'test/Ngn.MtClassUsage.js'
      ]
    ];
    Sflm::frontend('js')->addClass('Ngn.MtClassUsage');
    $this->assertTrue((bool)strstr(Sflm::frontend('js')->code(), 'Fx.CSS = new Class'));
  }

}