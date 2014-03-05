<?php

require_once VENDORS_PATH.'/DbSimple/Mysql.php';

if (!defined('DB_NAME')) {
  if (file_exists(NGN_ENV_PATH.'/config/database.php')) require NGN_ENV_PATH.'/config/database.php';
}

class Db extends DbSimple_Mysql {

  private $user;
  private $pass;
  private $host;
  public $name;
  public $charset;
  public $collate = 'utf8_general_ci';

  function __construct($user, $pass, $host, $name, $charset = 'utf8') {
    $this->user = $user;
    $this->pass = $pass;
    $this->host = $host;
    $this->name = $name;
    parent::__construct('mysql://'.$user.':'.$pass.'@'.$host.'/'.$name);
    //output('connected '.$this->link);
    $this->setErrorHandler(['Err', 'sql']);
    $this->charset = $charset;
    $this->query('SET NAMES '.$charset);
  }

  function disconnect() {
    @mysql_close($this->link);
    unset($this->link);
  }

  /*
  protected function _query($query, &$total) {
    output(getPrr($query));
    if (!$this->link) {
      output("no link ".$this->link);
      throw new Exception('No link');
    }
    parent::_query($query, $total);
  }
  */

  function create($table, array $data, $replace = false) {
    return $this->query(($replace ? 'REPLACE' : 'INSERT')." INTO $table SET ?a", $data);
  }

  function getHost() {
    return $this->host;
  }

  function getName() {
    return $this->name;
  }

  function q($q) {
    return $this->select($q);
  }

  function row($q) {
    return $this->selectRow($q);
  }

  function col($q) {
    return $this->selectCol($q);
  }

  function cell($q) {
    return $this->selectCell($q);
  }

  /**
   * Возвращает массив с именами всех таблиц NGN
   */
  static function _tables($name, $link) {
    // Определить является ли таблица, таблицой NGN
    // А всё просто. Если есть префикс, значит все с префиксом.
    // Если нет префикса, то тогда просто все...
    $tables = [];
    $r = mysql_query('SHOW FULL TABLES FROM '.$name, $link);
    while (($row = mysql_fetch_row($r))) {
      if ($row[1] != 'VIEW') $tables[] = $row[0];
    }
    return $tables;
  }

  function tables() {
    return $this->_tables($this->name, $this->link);
  }

  function cols($table) {
    return $this->selectCol("SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=? AND TABLE_NAME=?", $this->name, $table);
  }

  function colTypes($table) {
    return $this->selectCol("
    SELECT COLUMN_NAME AS ARRAY_KEY, DATA_TYPE
    FROM information_schema.`COLUMNS`
    WHERE TABLE_SCHEMA=? AND TABLE_NAME=?", $this->name, $table);
  }



  function fieldExists($table, $name) {
    return in_array($name, $this->fields($table));
  }

  function fields($table) {
    return $this->selectCol('SHOW COLUMNS FROM '.$table);
  }

  function rename($from, $to) {
    return $this->query("RENAME TABLE `$from` TO `$to`");
  }

  function renameField($table, $from, $to) {
    $types = Arr::get($this->query("SHOW COLUMNS FROM $table"), 'Type', 'Field');
    $this->query("ALTER TABLE `$table` CHANGE `$from` `$to` {$types[$from]}");
  }

  function replace($from, $to) {
    $this->query("DROP TABLE IF EXISTS $to");
    return $this->query("RENAME TABLE `$from` TO `$to`");
  }

  function exists($table) {
    return in_array($table, $this->tables());
  }

  static function dbExists($db, $config = null) {
    if (defined('NGN_ENV_PATH')) $c = include NGN_ENV_PATH.'/config/server.php';
    Arr::checkEmpty($c, ['dbHost', 'dbUser', 'dbPass']);
    $id = mysql_connect($c['dbHost'], $c['dbUser'], $c['dbPass']);
    $r = mysql_query("SHOW DATABASES", $id);
    while (($row = mysql_fetch_assoc($r)) !== false) if ($row['Database'] == $db) return true;
    return false;
  }

  function copy($from, $to) {
    $this->query("DROP TABLE IF EXISTS $to");
    $this->query("CREATE TABLE $to LIKE $from");
    $this->query("INSERT INTO $to SELECT * FROM $from");
  }

  function copyStructure($from, $to) {
    $this->query("DROP TABLE IF EXISTS $to");
    $this->query("CREATE TABLE $to LIKE $from");
  }

  function copyPrefixed($prefix) {
    foreach ($this->tables() as $t) $this->copy($t, $prefix.'_'.$t);
  }

  function deletePrefixed($prefix) {
    foreach ($this->tables() as $t) if (preg_match('/^'.$prefix.'_.*/', $t)) $this->query("DROP TABLE $t");
  }

  function insert($table, array $data, $replace = false) {
    return $this->query(($replace ? 'REPLACE' : 'INSERT')." INTO $table SET ?a", $data);
  }

  public $insertIgnore = false;

  function insertLarge($table, $rows/*, array $opts = []*/) {
    if (empty($rows)) throw new Exception('$rows is empty');
    $keys = array_keys($rows[0]);
    if ($keys[0] === 0) throw new Exception("First element of $rows must be a hash. '{$keys[0]}' given");
    $q = 'INSERT './*(empty($opts['ignore']) ? '' : "{$opts['ignore']} ").*/'INTO '.$table.' ('.implode(', ', array_keys($rows[0])).") VALUES \n";
    foreach ($rows as $row) {
      array_walk($row, 'quoting');
      $q .= '('.implode(', ', $row)."),\n";
    }
    $q[strlen($q) - 2] = ';';
    $this->query($q);
  }

  //=============================================================

  function backup() {
    foreach ($this->tables() as $table) {
      if (!strstr($table, 'bak_')) {
        $this->copy($table, 'bak_'.$table);
      }
    }
  }

  function restore() {
    foreach ($this->tables() as $table) {
      if (strstr($table, 'bak_')) {
        $this->copy($table, str_replace('bak_', '', $table));
      }
    }
    $this->deleteBackup();
  }

  function deleteBackup() {
    foreach ($this->tables() as $table) {
      if (strstr($table, 'bak_')) $this->query("DROP TABLE $table");
    }
  }

  /**
   * Возвращает строку с дампом
   *
   * @param   null/array null, если нужно экспортировать все таблицы или массив
   *          с именами таблиц елси нужно экспортировать только конкретные
   * @return  string
   */
  function export($toFile, $tables = null) {
    $oDbDumper = new DbDumper($this);
    $oDbDumper->setDroptables(true);
    $oDbDumper->insertGroupLimit = 10;
    return $oDbDumper->createDump($toFile, $tables);
  }

  private $importLogger;

  function setImportLogger($importLogger) {
    $this->importLogger = $importLogger;
  }

  public $importFileSizeLimit = 0;

  function importFile($file) {
    sys("mysql -h{$this->host} -u{$this->user} -p{$this->pass} --default_character_set utf8 {$this->name} < $file");
  }

  function importFile___OLD($file) {
    $this->setErrorHandler(['Err', 'sqlDie']);
    $fp = fopen($file, 'r');
    $q = '';
    $bytes = 0;
    while (($c = fread($fp, 512)) !== false) {
      $bytes += 1024;
      if ($this->importFileSizeLimit and $bytes > $this->importFileSizeLimit) {
        $q = '';
        break;
      }
      print '';
      $c = str_replace("\r", '', $c);
      if (strstr($c, ";\n")) {
        $querys = explode(";\n", $c);
        $querys[0] = $q.$querys[0];
        $q = count($querys) > 1 ? $querys[count($querys) - 1] : '';
        for ($i = 0; $i < count($querys) - 1; $i++) {
          $this->import($querys[$i]);
        }
      }
      else {
        $q .= $c;
      }
    }
    if ($q != '') $this->import($q);
    $this->setErrorHandler(['Err', 'sql']);
  }

  function import($sql) {
    $startTime = getMicrotime();
    $sql = str_replace("\r", '', $sql);
    $sql = trim(preg_replace('/^\s*#.*$\n/m', '', $sql));
    $sql = trim(preg_replace('/^\s*--.*$\n/m', '', $sql));
    $disabledKeysTables = [];
    foreach (explode(";\n", $sql) as $query) {
      if (trim($query)) {
        if ($this->importLogger and is_callable($this->importLogger)) call_user_func($this->importLogger, $query);
        /*
        if (preg_match('/INSERT\s+INTO\s+`*([^\s^`]*)`* /', $query, $m)) {
          $table = $m[1];
          if (!in_array($table, $disabledKeysTables)) {
            $this->q("ALTER TABLE $table DISABLE KEYS");
            $disabledKeysTables[] = $table;
          }
        }
        */
        mysql_query($query, $this->link);
        // $this->q($query); // почему то вызывает рекурсивный вызов себя
      }
    }
    /*
    foreach ($disabledKeysTables as $table) {
      $this->q("ALTER TABLE $table ENABLE KEYS");
    }
    */
    $processTime = round(getMicrotime() - $startTime, 3);
  }

  function delete($tables = null, $strict = true) {
    $tables = (array)$tables;
    foreach ($this->tables() as $table) {
      if ($tables and !in_array($table, $tables)) continue;
      $this->query("DROP TABLE ".($strict ? '' : 'IF EXISTS ')."$table");
    }
  }

  function deleteCol($table, $colName, $strict = false) {
    if (!$strict and !in_array($colName, $this->cols($table))) return;
    $this->query("ALTER TABLE $table DROP $colName");
  }

  function deleteRow($table, $id) {
    $this->query("DELETE FROM $table WHERE id=?d", $id);
  }

  function update($table, $id, array $data) {
    Misc::checkEmpty($data);
    $this->query("UPDATE $table SET ?a WHERE id=?d", $data, $id);
  }

  function setPrefix($prefix) {
    foreach ($this->tables() as $t) $this->rename($t, $prefix.'_'.$t);
  }

  function removePrefix($prefix) {
    foreach ($this->tables() as $t) $this->rename($t, str_replace($prefix.'_', '', $t));
  }

  function ids($table, $cond = null) {
    if (!$cond) return $this->selectCol("SELECT id FROM ".$table);
    else
      return $this->selectCol("SELECT $table.id FROM ".$table.$cond->all());
  }

  function firstId($table) {
    return $this->selectCell("SELECT id FROM $table ORDER BY id LIMIT 1");
  }

  function prepareQuery($query, array $_args = []) {
    if (count($_args) > 1) {
      $args[0] = $query;
      $args = Arr::append($args, $_args);
      $this->_expandPlaceholders($args);
      $query = $args[0];
    }
    return $query;
  }

  function getAndCond($params) {
    if (!count($params)) return '1';
    return Tt()->enum($params, ' AND ', "\\\"\$k=`\$v`\\\""); // Шаблон преобразовывается в "$k='$v'"
  }

  function unpack($query) {
    $rows = $this->query($query);
    foreach ($rows as &$row) {
      foreach ($row as &$v) {
        if (Arr::unserializeble($v)) $v = unserialize($v);
      }
    }
    return $rows;
  }

  public $multiInsertReplace = false;

  function multiInsertAddIdColumn($table, $data, $rowsPerQuery = 200) {
    $n = 0;
    $id = $this->selectCell("SELECT id FROM $table ORDER BY id DESC LIMIT 1");
    foreach ($data as $v) {
      $id++;
      $data2[$n]['id'] = $id;
      $data2[$n] += $v;
      $n++;
    }
    $this->multiInsert($table, $data2, $rowsPerQuery);
  }

  function multiInsert($table, $data, $rowsPerQuery = 200) {
    $portion = [];
    for ($i = 0; $i < count($data); $i++) {
      $portion[] = $data[$i];
      if (($i + 1) % $rowsPerQuery == 0) {
        $this->_multiInsert($table, $portion);
        $portion = [];
      }
    }
    if ($portion) $this->_multiInsert($table, $portion);
  }

  protected function _multiInsert($table, $data) {
    if (!$data) return;
    $rows = '';
    foreach ($data as $row) {
      foreach ($row as &$v) $v = "'$v'";
      $rows[] = '('.implode(', ', $row).')';
    }
    $q = ($this->multiInsertReplace ? 'REPLACE' : 'INSERT')." INTO $table VALUES ".implode(', ', $rows).';';
    $this->query($q);
  }

  function getCell($table, $field1, $field2, $value) {
    return db()->selectCell("SELECT $field1 FROM $table WHERE $field2='$value'");
  }

  function getCell2($table, $field, DbCond $cond) {
    return db()->selectCell("SELECT $field FROM $table".$cond->all());
  }

  function increment($table, array $unicData, array $data = [], $field = 'cnt') {
    $cond = DbCond::get();
    foreach ($unicData as $k => $v) $cond->addF($k, $v);
    if ($this->getCell2($table, $field, $cond)) {
      $this->query("UPDATE $table SET $field=$field+1".(empty($data) ? '' : ', ?a').$cond->all(), $data);
    }
    else {
      $data = array_merge($unicData, $data);
      $data[$field] = 1;
      $this->create($table, $data);
    }
  }

  // ------------------ static -------------------

  static function createDb($user, $pass, $host, $name) {
    if (!mysql_connect($host, $user, $pass)) throw new Exception("Can not connect. User='$user', Pass='$pass', Host='$host'");
    mysql_query("CREATE DATABASE IF NOT EXISTS $name COLLATE ".DB_COLLATE);
  }

  static function deleteDb($user, $pass, $host, $name) {
    if (!mysql_connect($host, $user, $pass)) throw new Exception("Can not connect. User='$user', Pass='$pass', Host='$host'");
    mysql_query('DROP DATABASE '.$name);
  }

  static function getReservedMySQLwords() {
    if (!($words = file(LIB_PATH.'/more/dd/reservedMySQLwords.txt'))) throw new Exception('Can not open "reservedMySQLwords.txt"');
    for ($i = 0; $i < count($words); $i++) $words[$i] = strtolower(trim($words[$i]));
    return $words;
  }

  static function getReservedNames() {
    return Arr::append(Db::getReservedMySQLwords(), [
        'dateCreate', 'dateUpdate', 'pageId', 'ip', 'id', 'action', 'oid', 'age'
      ]);
  }

  static function isReservedMySQLword($word) {
    return in_array(strtolower($word), Db::getReservedMySQLwords()) ? true : false;
  }

  static function normalize($s) {
    $s = Misc::transit($s);
    $s = Misc::hyphenate($s);
    $s = str_replace(' ', '_', $s);
    return str_replace('-', '_', $s);
  }

  static function getSize(Db $db = null) {
    if (!$db) $db = db();
    $key = 'dbSize'.$db->name;
    if (($r = FileCache::c()->load($key)) !== false) return $r;
    $r =  round($db->selectCell("
SELECT
  SUM(DATA_LENGTH + INDEX_LENGTH) AS size
FROM information_schema.TABLES
WHERE table_schema = '{$db->name}'
GROUP BY TABLE_SCHEMA
"));
    FileCache::c()->save($r, $key);
    return $r;
  }

  function getNextId($table) {
    return $this->selectCell("SELECT id FROM $table ORDER BY id DESC");
  }

  function getRow($table, $id) {
    return $this->selectRow("SELECT * FROM $table WHERE id=?d", $id);
  }

}
