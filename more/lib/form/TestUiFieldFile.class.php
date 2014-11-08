<?php

class TestUiFieldFile extends TestDd {

  static function enable() {
    return false;
  }

  static function setUpBeforeClass() {
    parent::setUpBeforeClass();
    O::di('DdFieldsManager', 'a')->create([
      'title' => 'sample',
      'name' => 'sample',
      'type' => 'file',
      'required' => true
    ]);
    O::di('DdFieldsManager', 'a')->create([
      'name' => 'invalid',
      'type' => 'invalid',
    ]);
    $p = PROJECT_KEY;
    `pm localProject cc $p`;
  }

  function test() {
    DdCore::imDefault('a')->create(['sample' => TestCore::tempImageFixture()]);
    Casper::run(PROJECT_KEY, [
      'auth',

      // ссылка на сохраненный файл есть
      ['thenUrl', 'god/ddItems/a/edit/1'],
      ['checkExistence', '.fileSaved'],
      ['wait', 1000],
      ['fill', 'form', ['sample' => TestCore::tempImageFixture()['tmp_name'], 'invalid' => 1], true],
      ['wait', 1000],

      // ссылка на загруженный файл есть, при серверной инвалидации
      ['thenUrl', 'god/ddItems/a/edit/1'],
      ['wait', 1000],
      ['checkExistence', '.fileSaved'],
      ['checkExistence', '.fileUploaded'],
      ['wait', 1000],

      // ссылки на загруженный файл нет, при успешном сабмите на стороне сервера
      ['thenUrl', 'god/ddItems/a/edit/1'],
      ['fill', 'form', [
        'sample' => TestCore::tempImageFixture()['tmp_name'],
        'invalid' => 'valid'
      ], true],
      ['wait', 1000],

      // после успешного сабмита загруженного файла быть не должно
      ['thenUrl', 'god/ddItems/a/edit/1'],
      ['!checkExistence', '.fileUploaded'],

      // сабмитим форму. должна отправиться, т.к. картинка в поле уже сохранена
      ['thenUrl', 'god/ddItems/a/edit/1'],
      ['wait', 1000],
      ['fill', 'form', [
        'sample' => '',
        'invalid' => ''
      ], true],
      ['wait', 1000],
      ['!checkExistence', 'form'],
    ]);
  }

}