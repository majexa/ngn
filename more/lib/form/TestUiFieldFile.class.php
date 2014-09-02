<?php

class TestUiFieldFile extends TestFieldDdBase {

  static function setUpBeforeClass() {
    parent::setUpBeforeClass();
    O::gett('DdFieldsManager', 'a')->create([
      'name' => 'invalid',
      'type' => 'invalid',
    ]);
  }

  function test() {
    DdCore::imDefault('a')->create(['sample' => TestRunnerNgn::tempImageFixture()]);
    Casper::run(PROJECT_KEY, [
      'auth',

      ['thenUrl', 'god/ddItems/a/edit/1'],

      /*
      // ссылка на сохраненный файл есть
      ['checkText', '.fileSaved', 'сохранён (96.02 Кб)'],
      ['wait', 1000],
      ['fill', 'form', [
        'sample' => TestRunnerNgn::tempImageFixture()['tmp_name'],
        'invalid' => 1
      ], true],
      ['wait', 2000],
      // ссылка на загруженный файл есть, при серверной инвалидации
      ['thenUrl', 'god/ddItems/a/edit/1'],
      ['wait', 1000],
      ['checkText', '.fileSaved', 'сохранён (96.02 Кб)'],
      ['checkText', '.fileUploaded', 'загружен (96.02 Кб)'],
      ['wait', 1000],
      // ссылка на загруженный файл нет, при успешном сабмите на стороне сервера
      ['thenUrl', 'god/ddItems/a/edit/1'],
      ['fill', 'form', [
        'sample' => TestRunnerNgn::tempImageFixture()['tmp_name'],
        'invalid' => 'valid'
      ], true],
      ['wait', 2000],
      ['thenUrl', 'god/ddItems/a/edit/1'],
      ['!checkExistence', '.fileUploaded'],
      */

      ['fill', 'form', [
        'sample' => '',
        'invalid' => ''
      ], true],
      ['wait', 2000],
      ['thenUrl', 'god/ddItems/a/edit/1'],
      ['checkExistence', '.fileSaved'],
    ]);
  }

}