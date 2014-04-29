<?php

class DdiCache extends FileCache {

  function __construct(array $options) {
    Arr::checkEmpty($options, 'strName');
    parent::__construct($options);
  }

  static function folder(array $options) {
    return DATA_PATH.'/ddiCache/'.$options['strName'];
  }

}