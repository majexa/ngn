<?php

class DbShift {

  /**
   * Перемещает запись таблицы, используя OID
   *
   * @param integer Уникальный ID записи в таблице с именем поля 'id'
   * @param string  Действие 'up', 'down'
   * @param string  Имя таблицы
   * @param string  Дополнительное условие запроса.
   *                Массив формата: array('k' => 'key', 'v' => 'value')
   */
  static function item($id, $action, $table, $filters = null, $idName = 'id') {
    if (!$id = (int)$id) return;
    if ($action != 'up') $action = 'down';
    $fltr = '';
    foreach ($filters as $k => $v) {
      if (is_string($v)) $v = "'".mysql_real_escape_string($v)."'";
      $fltr .= " AND $k=$v";
    }
    $moveIds = db()->selectCol("
               SELECT $idName FROM $table WHERE
                 1
                 $fltr
               ORDER BY oid");
    for ($i = 0; $i < count($moveIds); $i++) {
      if ($moveIds[$i] == $id) {
        if ($action == 'up') {
          if (!$moveIds[$i - 1]) continue;
          $curId = $moveIds[$i - 1];
          $moveIds[$i - 1] = $id;
          $moveIds[$i] = $curId;
        }
        else {
          if (!$moveIds[$i + 1]) continue;
          $curId = $moveIds[$i + 1];
          $moveIds[$i + 1] = $id;
          $moveIds[$i] = $curId;
        }
        break;
      }
    }
    die2($moveIds);
    DbShift::items($moveIds, $table, $idName);
  }

  static function items(array $moveIds, $table, $idName = 'id') {
    for ($i = 0; $i < count($moveIds); $i++) {
      //db()->_logger = function($a) { prr(Arr::first($a->_placeholderCache)[0]); };
      //prr(["UPDATE $table SET oid=?d WHERE $idName=?d", ($i + 1) * 10, $moveIds[$i]]);
      db()->query("UPDATE $table SET oid=?d WHERE $idName=?d", ($i + 1) * 10, $moveIds[$i]);
      //db()->_logger = null;
    }
  }

  static function sort($table, DbCond $cond) {
    $cond->setOrder('oid');
    $ids = db()->ids($table, $cond);
    //prr([$ids, $cond->all()]);
    for ($i = 0; $i < count($ids); $i++) {
      //print "<p>UPDATE $table SET oid=?d WHERE id=?d === ".(($i + 1) * 10).', '.$ids[$i].'</p>';
      db()->query("UPDATE $table SET oid=?d WHERE id=?d", ($i + 1) * 10, $ids[$i]);
    }
  }

}
