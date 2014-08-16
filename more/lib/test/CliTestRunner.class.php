<?php

class CliTestRunner extends CliHelpArgs {

  function prefix() {
    return false;
  }

  function getClasses() {
    return [
      [
        'class' => 'TestRunnerProject',
        'name' => 'proj',
        'title' => 'Интерфейс для запуска тестов на уровне проекта'
      ],
      [
        'class' => 'TestRunnerNgn',
        'name' => 'ngn',
        'title' => 'Интерфейс для запуска тестов фреймворка'
      ],
      [
        'class' => 'TestRunnerLib',
        'name' => 'lib',
        'title' => 'Интерфейс для запуска тестов библиотек'
      ],
      [
        'class' => 'TestRunnerPlib',
        'name' => 'plib',
        'title' => 'Интерфейс для запуска тестов библиотек на уровне проекта'
      ],
      [
        'class' => 'TestCliCommon',
        'name' => 'c',
        'title' => 'Разное'
      ],
    ];
  }

  protected function _runner() {
    return 'tst';
  }

}