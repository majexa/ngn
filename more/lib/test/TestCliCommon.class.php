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
    print O::get('CliColors')->getColoredString('tst c proj:', 'yellow')."\n";
    foreach ((new TestRunnerProject('dummy'))->_g() as $class) print ClassCore::classToName('Test', $class)."\n";
    print O::get('CliColors')->getColoredString('tst ngn run:', 'yellow')."\n";
    foreach ((new TestRunnerNgn)->_getClasses() as $class) print ClassCore::classToName('Test', $class)."\n";
  }

  /**
   * Создаёт проект "test"
   */
  function createProject() {
    $server = require NGN_ENV_PATH.'/config/server.php';
    $domain = 'test.'.$server['baseDomain'];
    print `pm localServer createProject test $domain common`;
    print `pm localProject replaceConstant test core IS_DEBUG true`;
    print `pm localProject cc test`;
  }

  /**
   * Создаёт проект "test" и запускает для него глобальные проектные тесты
   */
  function g($filterNames = null) {
    print `pm localProject delete test`;
    $this->createProject();
    $filterNames = $filterNames ? ' '.$filterNames : '';
    print `tst proj g test$filterNames`;
  }

  /**
   * Создаёт проект "test" и запускает casper-тесты
  function casper($filterNames = null) {
    $this->createProject();
    $filterNames = $filterNames ? ' '.$filterNames : '';
    print `tst proj g test$filterNames`;
    //print `pm localProject delete test`;
  }
   */

}