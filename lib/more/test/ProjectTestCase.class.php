<?php

/**
 * Тесты, запускаемые в окружении проекта
 */
class ProjectTestCase extends NgnTestCase {

  static $local = false;

  static function enable() {
    return true;
  }

}