<?php

class SflmCli {

  function cc() {
    Sflm::clearCache();
  }

  /**
   * Отображает пути sflm-фронтенда находящиеся в кэше данных
   */
  function paths($frontend, $type) {
    Sflm::setFrontendName($frontend);
    print implode("\n", Sflm::frontend($type)->getPaths())."\n";
  }

  /**
   * Отображает пути sflm-фронтенда находящиеся в веб-кэше
   */
  function webPaths($frontend, $type) {
    Sflm::setFrontend($type, $frontend)->initStore();
    foreach (array_count_values(Sflm::setFrontend($type, $frontend)->parseWebCachePaths()) as $path => $count) {
      print ($count == 1 ? $path : CliColors::colored($path.' {'.$count.'}', 'yellow'))."\n";
    }
  }

  /**
   * Отображает версию sflm-фронтенда
   */
  function version($frontend, $type) {
    Sflm::setFrontendName($frontend);
    print Sflm::frontend($type)->version()."\n";
  }

  /**
   * Отображает js-классы sflm-фронтенда
   */
  function jsClasses($frontend) {
    Sflm::setFrontendName($frontend);
    foreach ((new SflmJsClassPaths) as $class => $path) {
      print str_pad($class, 50).$path."\n";
    }
  }

  static function helpOpt_frontend() {
    return [
      'default',
      'admin'
    ];
  }

}