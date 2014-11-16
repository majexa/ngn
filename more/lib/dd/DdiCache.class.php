<?php

// @todo очищать ddi кеш после добавления поля
class DdiCache extends FileCache {

  function __construct(array $options) {
    Arr::checkEmpty($options, 'strName');
    parent::__construct($options);
  }

  static function folder(array $options) {
    Dir::make(DATA_PATH.'/ddiCache/'.$options['strName']);
    return DATA_PATH.'/ddiCache/'.$options['strName'];
  }

}