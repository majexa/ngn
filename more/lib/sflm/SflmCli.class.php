<?php

class SflmCli {

  /**
   * Отображает пути фронтенда
   */
  function paths($frontend, $type) {
    Sflm::setFrontendName($frontend);
    print implode("\n", Sflm::frontend($type)->getPaths())."\n";
  }

  function version($frontend, $type) {
    Sflm::setFrontendName($frontend);
    print Sflm::frontend($type)->version()."\n";
  }

}