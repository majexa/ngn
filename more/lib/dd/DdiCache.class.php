<?php

class DdiCache extends FileCache {

  function __construct(array $options) {
    Arr::checkEmpty($options, 'strName');
    parent::__construct($options);
  }

  protected function folder() {
    return DATA_PATH.'/ddiCache/'.$this->options['strName'];
  }

}