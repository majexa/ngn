<?php

class Errors {

  function get() {
    return Arr::append(LogReader::get('warnings'), LogReader::get('errors'));
  }

  /**
   * Очищает все логи с ошибками в LOGS_PATH
   * @cmd run
   */
  function clear() {
    File::delete(LOGS_PATH.'/r_warnings.log');
    File::delete(LOGS_PATH.'/r_errors.log');
  }

}