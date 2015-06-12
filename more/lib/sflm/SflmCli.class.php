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
  function paths2($frontend, $type) {
    $webPackageFile = Sflm::$webPath.'/'.$type.'/cache/'.$frontend.'.'.$type;
    preg_match_all('/\\/\\*--\\|(.*)\\|--\\*\\//', file_get_contents($webPackageFile), $m);
    foreach (array_count_values($m[1]) as $path => $count) {
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