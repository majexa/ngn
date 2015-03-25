<?php

class SflmCli {

  /**
   * Отображает пути sflm-фронтенда
   */
  function paths($frontend, $type) {
    Sflm::setFrontendName($frontend);
    print implode("\n", Sflm::frontend($type)->getPaths())."\n";
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