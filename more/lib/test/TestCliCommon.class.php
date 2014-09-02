<?php

/**
 * Разное
 */
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
    $columns = [[],[]];
    $columns[0][] = 'tst proj g {name}:';
    foreach ((new TestRunnerProject('dummy'))->_g() as $class) {
      $columns[0][] = ClassCore::classToName('Test', $class);
    }
    $columns[1][] ='tst ngn run:';
    foreach ((new TestRunnerNgn)->_getClasses() as $class) {
      $columns[1][] = ClassCore::classToName('Test', $class);
    }
    print Cli::columns($columns);
  }

  /**
   * Создаёт проект "test"
   */
  function createProject($type = 'common') {
    print `pm localServer deleteProject test`;
    print `pm localServer createProject test default $type`;
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

}