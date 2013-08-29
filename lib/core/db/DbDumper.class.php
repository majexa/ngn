<?php

class DbDumper {

  public $droptables = false, $dumpStructure = true, $dumpData = true, $autoIncrementToNull = true, $cond;

  /**
   * @var Db
   */
  private $db;

  function __construct($db = null) {
    $this->db = $db ? : db();
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

  /**
   * Делает дамп базы
   *
   * @param   string  Файл для экспорта
   * @param   array   Таблицы для дампа или null, если нужен дамп всех таблиц базы
   * @return  string  Дамп
   */
  function createDump($toFile, $onlyTables = null) {
    // Set line feed 
    $lf = "\n";
    $result = mysql_query("SHOW TABLES", $this->db->link) or die(mysql_error());
    $tables = $this->result2Array(0, $result);
    $this->filterTables($tables);
    foreach ($tables as $table) {
      $result = mysql_query("SHOW CREATE TABLE `$table`");
      $createTable[$table] = $this->result2Array(1, $result);
    }
    if (file_exists($toFile)) unlink($toFile);
    if (!$fp = fopen($toFile, 'a')) throw new Exception('Can not open file "'.$toFile.'"');
    // Set header
    $dumpHeader = "#".$lf;
    $dumpHeader .= "# DbDumper SQL Dump".$lf;
    $dumpHeader .= "# Version 1.0".$lf;
    $dumpHeader .= "# ".$lf;
    $dumpHeader .= "# Host: ".$this->db->getHost().$lf;
    $dumpHeader .= "# Generation Time: ".date("M j, Y \\a\\t H:i").$lf;
    $dumpHeader .= "# Server version: ".mysql_get_server_info().$lf;
    if ($this->db->getName()) $dumpHeader .= "# Database : `".$this->db->getName()."`".$lf;
    $dumpHeader .= "#";
    fwrite($fp, $dumpHeader);
    $tablesN = 0;
    $groupN = 1;
    // Generate dumptext for the tables. 
    foreach ($tables as $table) {
      $tablesN++;
      if (isset($onlyTables) and !in_array($table, $onlyTables)) {
        continue;
      }
      $tableHeader = $lf.$lf."# --------------------------------------------------------".$lf.$lf;
      if ($this->dumpStructure) {
        if ($this->autoIncrementToNull) $createTable[$table][0] = preg_replace('/AUTO_INCREMENT=\d+ / ', '', $createTable[$table][0]);
        $tableHeader .= "#".$lf."# Table structure for table `$table`".$lf;
        $tableHeader .= "#".$lf.$lf;
        // Generate DROP TABLE statement when client wants it to. 
        if ($this->droptables()) {
          $tableHeader .= "DROP TABLE IF EXISTS `$table`;".$lf;
        }
        $tableHeader .= $createTable[$table][0].";".$lf;
        $tableHeader .= $lf;
        output("Table '$table' structure exported");
      }
      fwrite($fp, $tableHeader);
      if ($this->dumpData) {
        output('Dumping data for '.$table.' table');
        $tableDumpHeader = "#".$lf."# Dumping data for table `$table`".$lf."#".$lf;
        fwrite($fp, $tableDumpHeader);
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
          if ($rowN == 0) $insertDumpGroup = $lf."INSERT INTO `$table` VALUES";
          $insertDump = $lf."(";
          $arr = $row;
          foreach ($arr as $fieldName => $value) {
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
            if ($this->separateGroupFiles) {
              fclose($fp);
              $fp = fopen($this->getFilename($toFile, $groupN), 'w');
            }
            fwrite($fp, $insertDumpGroup);
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
          if ($this->separateGroupFiles) {
            fclose($fp);
            $fp = fopen($this->getFilename($toFile, $groupN), 'w');
          }
          $insertDumpGroup = rtrim($insertDumpGroup, ',').";";
          fwrite($fp, $insertDumpGroup);
        }
        if ($nn == 0) output("There is no data to dump in table '$table'");
        else
          output("Table '$table' data exported ($nn records)");
      }
    }
    fclose($fp);
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