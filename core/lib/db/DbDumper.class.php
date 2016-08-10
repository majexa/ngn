<?php

class DbDumper {
  use Options;

  public $droptables = false, $dumpStructure = true, $dumpData = true, $autoIncrementToNull = true, $cond;

  /**
   * @var Db
   */
  private $db;

  function __construct($db = null, array $options = []) {
    $this->setOptions($options);
    $this->db = $db ?: db();
    $this->cond = new DbCond;
  }

  // If set to true, it will generate 'DROP TABLE IF EXISTS'-statements for each table. 
  function setDroptables($state) {
    $this->droptables = $state;
  }

  private function droptables() {
    return $this->droptables;
  }

  public $excludeTables;

  public $excludeRule;

  public $includeRule;

  public $includeTables;

  public $recordsLimit = 0; // 0 - без лимита

  private function filterTables(&$tables) {
    $_tables = [];
    foreach ($tables as $t) {
      if (isset($this->excludeTables) and in_array($t, $this->excludeTables)) continue;
      if (isset($this->includeTables) and !in_array($t, $this->includeTables)) continue;
      if (isset($this->excludeRule) and preg_match('/'.$this->excludeRule.'/', $t)) continue;
      if (isset($this->includeRule) and !preg_match('/'.$this->includeRule.'/', $t)) continue;
      $_tables[] = $t;
    }
    $tables = $_tables;
  }

  public $insertGroupLimit = 10;

  public $separateGroupFiles = false;

  protected $fp, $toFile;

  protected $lf = "\n";

  protected function createTableRecord() {}

  /**
   * Делает дамп базы
   *
   * @param $toFile string Файл для экспорта
   * @param null $onlyTables string|array Таблицы для дампа или null, если нужен дамп всех таблиц базы
   * @throws Exception string Дамп
   */
  function createDump($toFile, $onlyTables = null) {
    $this->toFile = $toFile;
    if ($onlyTables) $onlyTables = (array)$onlyTables;
    // Set line feed
    $result = mysql_query("SHOW TABLES", $this->db->link) or die(mysql_error());
    $tables = $this->result2Array(0, $result);
    $this->filterTables($tables);
    if ($this->toFile !== false) {
      if (file_exists($this->toFile)) unlink($this->toFile);
      if (!$this->fp = fopen($this->toFile, 'a')) throw new Exception('Can not open file "'.$toFile.'"');
    }
    if (empty($this->options['noHeaders'])) {
      // Set header
      $dumpHeader = "#".$this->lf;
      $dumpHeader .= "# DbDumper SQL Dump".$this->lf;
      $dumpHeader .= "# Version 1.0".$this->lf;
      $dumpHeader .= "# ".$this->lf;
      $dumpHeader .= "# Host: ".$this->db->getHost().$this->lf;
      $dumpHeader .= "# Generation Time: ".date("M j, Y \\a\\t H:i").$this->lf;
      $dumpHeader .= "# Server version: ".mysql_get_server_info().$this->lf;
      if ($this->db->getName()) $dumpHeader .= "# Database : `".$this->db->getName()."`".$this->lf;
      $dumpHeader .= "#";
      $this->write($dumpHeader);
    }
    $tablesN = 0;
    // Generate dumptext for the tables.
    foreach ($tables as $table) {
      $tablesN++;
      if (isset($onlyTables) and !in_array($table, $onlyTables)) {
        continue;
      }
      $tableHeader = $this->lf.$this->lf."# --------------------------------------------------------".$this->lf.$this->lf;
      if ($this->dumpStructure) {
        $tableHeader .= $this->structureDump($table);
        $this->write($tableHeader);
      }
      if ($this->dumpData) $this->dataDump($table, $this->toFile);
    }
    if ($this->toFile !== false) fclose($this->fp);
  }

  function structureDump($table, $toFile = false) {
    $structureRecord = $this->result2Array(1, mysql_query("SHOW CREATE TABLE `$table`"))[0];
    $r = '';
    if ($this->autoIncrementToNull) {
      $structureRecord = preg_replace('/AUTO_INCREMENT=\d+ / ', '', $structureRecord);
    }
    $r .= $this->lf."# Table structure for table `$table`";
    $r .= "#".$this->lf.$this->lf;
    // Generate DROP TABLE statement when client wants it to.
    if ($this->droptables()) {
      $r .= "DROP TABLE IF EXISTS `$table`;".$this->lf;
    }
    $r .= $structureRecord.";".$this->lf;
    $r .= $this->lf;
    output("Table '$table' structure exported");
    if ($toFile) {
      touch($toFile);
      file_put_contents($toFile, $r, FILE_APPEND);
    }
    return $r;
  }

  function dataDump($table, $toFile = false) {
    $this->toFile = $toFile;
    if ($this->toFile and !isset($this->fp)) $this->fp = fopen($this->toFile, 'a');
    $groupN = 1;
    output('Dumping data for '.$table.' table');
    $tableDumpHeader = $this->lf."# Dumping data for table `$table`".$this->lf;
    if (empty($this->options['noHeaders'])) $this->write($tableDumpHeader);
    $emptifyFieldNames = [];
    if (isset($this->emptifyFieldTypes)) {
      // Имена полей, значение которых нужно будет заменить на пустые строки
      foreach ($this->db->colTypes($table) as $fieldName => $fieldType) if (in_array($fieldType, $this->emptifyFieldTypes)) $emptifyFieldNames[] = $fieldName;
    }
    if (in_array('id', $this->db->cols($table))) $this->cond->setOrder('id DESC');
    if ($this->recordsLimit) $this->cond->setLimit($this->recordsLimit);
    $q = "SELECT * FROM `$table`".$this->cond->all();
    $result = mysql_query($q);
    if ($result === false) throw new Exception(mysql_error());
    $rowN = 0;
    $insertDumpGroup = '';
    $nn = 0; // Общий счетчик количества экспортируемых записей по текущей таблице
    while (($row = mysql_fetch_assoc($result))) {
      if ($rowN == 0) $insertDumpGroup = $this->lf."INSERT INTO `$table` VALUES";
      $insertDump = $this->lf."(";
      $this->processRow($row);
      foreach ($row as $fieldName => $value) {
        if (!empty($emptifyFieldNames) and in_array($fieldName, $emptifyFieldNames)) {
          $value = 'dummy';
        }
        else {
          $value = addslashes($value);
          $value = str_replace("\n", '\r\n', $value);
          $value = str_replace("\r", '', $value);
        }
        $insertDump .= is_numeric($value) ? "$value, " : "'$value', ";
      }
      $rowN++;
      $nn++;
      if ($rowN == $this->insertGroupLimit) {
        $insertDump = rtrim($insertDump, ', ').");";
        $insertDumpGroup .= $insertDump;
        if ($this->separateGroupFiles and $this->toFile !== false) {
          fclose($this->fp);
          $this->fp = fopen($this->getFilename($this->toFile, $groupN), 'w');
        }
        $this->write($insertDumpGroup);
        $insertDumpGroup = '';
        $rowN = 0;
        $groupN++;
      }
      else {
        $insertDump = rtrim($insertDump, ', ')."),";
        $insertDumpGroup .= $insertDump;
      }
    }
    if ($insertDumpGroup) {
      if ($this->separateGroupFiles and $this->toFile !== false) {
        fclose($this->fp);
        $this->fp = fopen($this->getFilename($this->toFile, $groupN), 'w');
      }
      $insertDumpGroup = rtrim($insertDumpGroup, ',').";";
      $this->write($insertDumpGroup);
    }
    if ($nn == 0) output("There is no data to dump in table '$table'");
    else output("Table '$table' data exported ($nn records)");
    $this->write($this->lf);
    return $toFile === false ? $this->write : null;
  }

  protected function processRow(array &$row) {
  }

  protected $write = '';

  protected function write($str) {
    $this->toFile === false ? $this->write .= $str : fwrite($this->fp, $str);
  }

  function getDump($onlyTables = null) {
    $this->createDump(false, $onlyTables);
    return $this->write;
  }

  private function getFilename($filename, $n) {
    return preg_replace('/(\w+)(\.\w+)/', '$1-'.sprintf('%04d', $n).'$2', $filename);
  }

  private function object2Array($obj) {
    $array = null;
    if (is_object($obj)) {
      $array = [];
      foreach (get_object_vars($obj) as $key => $value) {
        if (is_object($value)) $array[$key] = $this->object2Array($value);
        else
          $array[$key] = $value;
      }
    }
    return $array;
  }

  private function loadObjectList($key = '', $resource) {
    $array = [];
    while (($row = mysql_fetch_object($resource))) {
      if ($key) $array[$row->$key] = $row;
      else
        $array[] = $row;
    }
    mysql_free_result($resource);
    return $array;
  }

  private function result2Array($numinarray = 0, $resource) {
    $array = [];
    while (($row = mysql_fetch_row($resource))) {
      $array[] = $row[$numinarray];
    }
    mysql_free_result($resource);
    return $array;
  }

  protected $emptifyFieldTypes;

  function setEmptifyFieldTypes(array $types) {
    $this->emptifyFieldTypes = $types;
  }

}