<?php

class DdiCache extends FileCache {

  function __construct(array $options) {
    Arr::checkEmpty($options, 'strName');
    parent::__construct($options);
  }

  static function folder(array $options) {
    return Dir::make(DATA_PATH.'/ddiCache/'.$options['strName']);
  }

  /**
   * Удаляет кэш для всех структур
   */
  static function cleanAll() {
    Dir::clear(DATA_PATH.'/ddiCache/', true);
  }

}