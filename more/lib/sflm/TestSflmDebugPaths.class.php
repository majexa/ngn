<?php

class TestSflmDebugPaths extends ProjectTestCase {

  protected function setUp() {
    Sflm::clearCache();
    Sflm::setFrontend('js', 'default');
  }

//  function testDebugPathOnAddingClass() {
//    // добавляем часть пути класса в список отладочных путей
//    Sflm::$debugPaths = [
//      'js' => [
//        'test/Ngn.Sub.B.js'
//      ]
//    ];
//    // добавляем класс
//    Sflm::frontend('js')->addClass('Ngn.Sub.B');
//    // сохраняем собранный файл фронтенда
//    Sflm::frontend('js')->store();
//    // переопределяем фронтенд
//    Sflm::setFrontend('js');
//    // добавляем класс
//    Sflm::frontend('js')->addClass('Ngn.Sub.B');
//    // сохраняем собранный файл фронтенда
//    Sflm::frontend('js')->store();
//    // отдельный тег для файла отладочного класса есть
//    $this->assertTrue((bool)strstr(Sflm::frontend('js')->getTags(), 'Ngn.Sub.B'));
//    // отладочного класса не должно быть в основном файле
//    $this->assertFalse((bool)strstr(Sflm::frontend('js')->_code(), 'Ngn.Sub.B'));
//    // зато там должен быть Ngn.Sub.A от которого наследуется Ngn.Sub.B и которого нет в отладочных путях
//    $this->assertTrue((bool)strstr(Sflm::frontend('js')->_code(), 'Ngn.Sub.A'));
//  }

  /**
   * Тоже самое, что и предыдущий пример, только проверяем так же наличие файла родителя среди отладочных тэгов
   */
  function testDebugPathOnAddingNamespace() {
    // добавляем часть пути класса в список отладочных путей
    Sflm::$debugPaths = [
      'js' => [
        'Ngn.Sub'
      ]
    ];
    // добавляем класс
    Sflm::frontend('js')->addClass('Ngn.Sub.B');
    // сохраняем собранный файл фронтенда
    Sflm::frontend('js')->store();
    // переопределяем фронтенд
    Sflm::setFrontend('js');
    // добавляем класс
    Sflm::frontend('js')->addClass('Ngn.Sub.B');
    // сохраняем собранный файл фронтенда
    Sflm::frontend('js')->store();
    // должен присутствовать отдельный тег так же для файла неймспейса
    $parentPos = strpos(Sflm::frontend('js')->getTags(), 'Ngn.Sub.js');
    $this->assertTrue($parentPos !== false);
    // ну и для самого класса, как и в предыдущем примере
    $classPos = strpos(Sflm::frontend('js')->getTags(), 'Ngn.Sub.B.js');
    $this->assertTrue($classPos !== false);
    // отладочные теги должны выводиться начиная с самого раннего родителя в неймспейсах
    $this->assertTrue($parentPos < $classPos);
    // обоих не должно быть в коде
    $this->assertFalse((bool)strstr(Sflm::frontend('js')->_code(), 'Ngn.Sub = '));
    $this->assertFalse((bool)strstr(Sflm::frontend('js')->_code(), 'Ngn.Sub.B = '));
  }

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