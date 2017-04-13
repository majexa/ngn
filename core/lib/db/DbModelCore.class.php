<?php

class DbModelCore {

  static $forceCache = true;

  /**
   * @api
   * Возвращает db-модель
   *
   * @param $table
   * @param $value
   * @param string $param
   * @return DbModel|bool
   * @throws Exception
   */
  static function get($table, $value, $param = 'id') {
    if ($param == 'id' and !$value) return false;
    $class = self::getClass($table);
    $o = O::get($class, $table, $value, $param);
    return empty($o->r) ? false : $o;
  }

  /**
   * @api
   * Возвращает db-модель. Если не существует выбрасывает исключение
   */
  static function take($table, $value, $param = 'id') {
    if (($r = self::get($table, $value, $param)) === false) throw new Exception('No such model');
    return $r;
  }

  /**
   * @api
   * Возвращает класс db-модели
   */
  static function getClass($table) {
    $class = 'DbModel'.ucfirst($table);
    return class_exists($class) ? $class : 'DbModel';
  }

  /**
   * @api
   * Создаёт запись с данными `$data` в таблице `$table` и возвращает ID записи.
   * Если `$filterByFields=true`, данные фильтруются по именам полей
   *
   * @param $table
   * @param array $data
   * @param bool $filterByFields
   * @return integer
   */
  static function create($table, array $data, $filterByFields = false) {
    $class = self::getClass($table);
    if ($class == 'DbModel') {
      return $class::create($table, $data);
    } else {
      if (!empty($class::$defaultCreateValues)) {
        foreach ($class::$defaultCreateValues as $k => $v) {
          if (!isset($data[$k])) $data[$k] = $v;
        }
      }
      /* @var $class DbModel */
      if (method_exists($class, 'beforeCreateUpdate')) $class::beforeCreateUpdate($data);
      if (method_exists($class, '_create')) {
        $id = $class::_create($data, $filterByFields);
      } else {
        $id = $class::create($table, $data, $filterByFields);
      }
      if (method_exists($class, 'afterCreateUpdate')) $class::afterCreateUpdate($id);
      return $id;
    }
  }

  /**
   * @api
   * То же самое, что и `create`, только возвращает созданную модель, а не ID
   */
  static function createAndGet($table, array $data, $filterByFields = false) {
    return self::get($table, self::create($table, $data, $filterByFields));
  }

  /**
   * @api
   * Создаёт модель, если она не существует и возвращает её
   */
  static function createOrGet($table, $param, array $data, $filterByFields = false) {
    if (($r = self::get($table, $data[$param], $param))) return $r;
    return self::createAndGet($table, $data);
  }

  /**
   * @api
   * Изменяет на строчку с `ID=$id` в таблице `$table` на основе данных `$data`.
   * Если $filterByFields=true, данные фильтруются по именам полей
   */
  static function update($table, $id, array $data, $filterByFields = false) {
    $class = self::getClass($table);
    if ($class == 'DbModel') {
      $class::update($table, $id, $data);
    } else {
      if (method_exists($class, 'beforeCreateUpdate')) $class::beforeCreateUpdate($data);
      if (method_exists($class, '_update')) $class::_update($id, $data, $filterByFields);
      else $class::update($table, $id, $data, $filterByFields);
      if (method_exists($class, 'afterCreateUpdate')) $class::afterCreateUpdate($id);
    }
  }

  static $replaceCreate;

  /**
   * @api
   * Если строка с `ID=$id` существует, выполняет `DbModelCore::update()`,
   * иначе `DbModelCore::create()`
   */
  static function replace($table, $id, array $data, $filterByFields = false) {
    if (self::get($table, $id)) {
      self::update($table, $id, $data, $filterByFields);
      self::$replaceCreate = false;
    } else {
      if (!ClassCore::getStaticProperty(self::getClass($table), 'hasAutoIncrement', false)) {
        $data['id'] = $id;
        self::create($table, $data, $filterByFields);
      } else {
        $id = self::create($table, $data, $filterByFields);
      }
      self::$replaceCreate = true;
    }
    return $id;
  }

  /**
   * @api
   * Удаляет строку
   */
  static function delete($table, $id) {
    db()->query("DELETE FROM $table WHERE id=?", $id);
    self::cc($table, $id);
  }

  static function deleteByCond($table, DbCond $cond) {
    foreach (db()->selectCol('SELECT id FROM '.$table.$cond->all()) as $id) self::delete($table, $id);
  }

  static function cacheId($table, $id) {
    return 'model'.$table.$id;
  }

  static function cc($table, $id) {
    $class = self::getClass($table);
    if (($ids = Memc::get('modelids'.$table)) !== false) {
      foreach ($ids as $k => $_id) {
        if ($_id == $id) {
          preg_match('/(\w)\/(.*)/', $k, $m);
          //                value  param
          O::delete($class, $m[2], $m[1]);
        }
      }
    }
    O::delete($class, $id, 'id');
    ProjMem::delete(self::cacheId($table, $id));
  }

  static $ids = [];

  static function getIdByParam($table, $param, $value) {
    if (!isset(self::$ids[$table])) self::$ids[$table] = Memc::get('modelids'.$table);
    return isset(self::$ids[$table][$param.'/'.$value]) ? self::$ids[$table][$param.'/'.$value] : false;
  }

  static function saveIdByParam($table, $param, $value, $id) {
    $ids = Memc::get('modelids'.$table);
    $ids[$param.'/'.$value] = $id;
    Memc::set('modelids'.$table, $ids);
    self::$ids[$table] = $ids[$param.'/'.$value];
  }

  const modeArray = 1;
  const modeObject = 2;

  /**
   * @api
   * Возвращает набор db-моделей
   */
  static function collection($table, DbCond $cond = null, $mode = self::modeArray) {
    $args = func_get_args();
    $cond = $cond ? $cond->all() : '';
    if ($mode == self::modeArray) {
      $items = db()->select("SELECT * FROM ".$table.$cond);
      $class = self::getClass($table);
      foreach ($items as $k => &$item) $class::unpack($item);
      return $items;
    } else {
      $ids = db()->selectCol("SELECT id FROM ".$table.$cond);
      $r = [];
      foreach ($ids as $id) $r[] = self::get($table, $id);
      return $r;
    }
  }

  /**
   * @api
   * Возвращает количество записей в таблице
   */
  static function count($table, DbCond $cond = null) {
    $cond = $cond ? $cond->all() : '';
    $key = sha1($table.$cond);
    $cnt = db()->selectCell("SELECT COUNT(*) FROM ".$table.$cond);
    return $cnt;
  }

  static function pagination($n, $table, DbCond $cond = null, $mode = self::modeArray) {
    list($pNums, $limit) = (new Pagination(['n' => $n]))->get($table, $cond);
    if (!$cond) $cond = DbCond::get();
    return [
      'DbItemsExtended' => DbModelCore::collection('users', $cond->setLimit($limit)),
      'pagination' => [
        'pNums' => $pNums
      ]
    ];
  }

}