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

    $this->getTestLibs();


    //return;

    $columns = [[],[],[],[]];
    $columns[0][] = 'tst proj g {name}:';
    foreach ((new TestRunnerProject('dummy'))->_g() as $class) {
      $columns[0][] = ClassCore::classToName('Test', $class);
    }
    $columns[1][] ='tst ngn run:';
    foreach ((new TestRunnerNgn)->_getClasses() as $class) {
      $columns[1][] = ClassCore::classToName('Test', $class);
    }
    /*
    $n = 2;
    foreach ($this->getTestLibs() as $name) {
      $columns[$n][] = "tst lib $name";
      foreach ((new TestRunnerLib(NGN_ENV_PATH.'/'.$name))->_getClasses() as $class) {
        $columns[1][] = ClassCore::classToName('Test', $class);
      }
      $n++;
    }

    foreach ($this->getTestPlibs() as $name) {
      $columns[$n][] = "tst plib $name";
      foreach ((new TestRunnerLib(NGN_ENV_PATH.'/'.$name))->_getClasses() as $class) {
        $columns[1][] = ClassCore::classToName('Test', $class);
      }
      $n++;
    }

    //$columns[3][] ='tst lib projectName $name';
    //$this->getTestLibs()
    //$this->getTestPlibs()
    //new TestRunnerLib();
    */

    print Cli::columns($columns, true);
  }

  /**
   * Возвращает имена корневых библиотек имеющих тесты
   */
  protected function _getTestLibs() {
    $r = [];
    foreach (glob(NGN_ENV_PATH.'/*') as $f) {
      $name = basename($f);
      if ($name == 'run') continue;
      if (Dir::getFilesR("$f/lib", 'Test*')) {
        $r[] = $name;
      }
    }
    return $r;
  }

  protected function getTestLibs() {
    return array_filter($this->_getTestLibs(), function($name) {
      return !file_exists(NGN_ENV_PATH."/$name/projectLib");
    });
  }

  /**
   * Возвращает имена корневых библиотек имеющих тесты
   */
  protected function getTestPlibs() {
    return array_filter($this->_getTestLibs(), function($name) {
      return file_exists(NGN_ENV_PATH."/$name/projectLib");
    });
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