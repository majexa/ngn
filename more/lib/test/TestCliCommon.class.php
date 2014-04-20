<?php

class TestCliCommon {

  /**
   * Удаляет ошибки
   */
  function clear() {
    chdir(NGN_ENV_PATH.'/run');
    Cli::shell('php run.php "(new AllErrors)->clear()"');
  }

  /**
   * Отображает все, существующие в среде тесты
   */
  function lst() {
    print O::get('CliColors')->getColoredString('proj g:', 'yellow')."\n";
    foreach ((new TestRunnerProject('dummy'))->_g() as $class) print ClassCore::classToName('Test', $class)."\n";
    print O::get('CliColors')->getColoredString('ngn run:', 'yellow')."\n";
    foreach ((new TestRunnerNgn)->_getClasses() as $class) print ClassCore::classToName('Test', $class)."\n";
  }

}