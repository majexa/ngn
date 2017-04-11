<?php

function logSql($db, $sql) {
  if (!($sqlData = R::get('sqlData'))) $sqlData = [];
  if (!preg_match('/^\s*--.*/', $sql)) {
    R::increment('sqlN');
    $sqlData[] = [
      'sql'       => $sql,
      'backtrace' => getBacktrace()
    ];
    R::set('sqlData', $sqlData);
  }
  else {
    if (isset($sqlData[count($sqlData) - 1])) {
      $sqlData[count($sqlData) - 1]['info'] = $sql;
      R::set('sqlData', $sqlData);
    }
  }
  LogWriter::html('sql', $sql);
}

class DbSite extends Db {

  function __construct() {
    if (!defined('DB_USER')) {
      Config::loadConstants('database');
    }
    parent::__construct(DB_USER, DB_PASS, DB_HOST, DB_NAME, DB_CHARSET);
    // Блокирует модификацию базы
    if (defined('DB_BLOCK_MODIF') and DB_BLOCK_MODIF === true) $this->blockModification = true;
    // Определяет префикс
    if (defined('DB_PREFIX')) $this->setIdentPrefix(DB_PREFIX.'_');
    // Определяет ф-ю для логирования запросов
    if (DB_LOGGING === true) $this->setLogger('logSql');
  }

  protected function _query($query, &$total) {
    if (empty($this->link)) {
      $this->disconnect();
      parent::__construct(DB_USER, DB_PASS, DB_HOST, DB_NAME, DB_CHARSET);
    }
    return parent::_query($query, $total);
  }

  function disconnect() {
    parent::disconnect();
    O::delete('DbSite');
  }

}