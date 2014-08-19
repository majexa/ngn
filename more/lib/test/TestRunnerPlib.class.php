<?php

/**
 * Запуск тестов библиотек на уровне проекта
 */
class TestRunnerPlib extends TestRunnerLib {

  function __construct($projectName, $libPath, $filterNames = null) {
    parent::__construct($libPath, $filterNames);
  }

}
