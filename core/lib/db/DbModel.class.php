<?php

class DbModel extends ArrayAccesseble {

  static $serializeble = [];

  static $hasAutoIncrement = true;

  static $hasDefaultDateFields = true;

  static $defaultCreateValues = [];

  protected $table, $value, $param;

  public $fromCache = false;

  function __construct($table, $value, $param = 'id') {
    Misc::checkEmpty($table);
    $this->table = $table;
    $this->value = $value;
    $this->param = $param;
    if ($this->param != 'id') {
      $id = DbModelCore::getIdByParam($table, $this->param, $this->value);
    }
    else {
      $id = $this->value;
    }
    if (!DbModelCore::$forceCache and $id !== false) {
      if (($this->r = ProjMem::get(DbModelCore::cacheId($table, $id))) !== false) {
        $this->fromCache = true;
        return;
      }
    }
    $this->r = $this->getModelData();
    if (!empty($this->r)) {
      Arr::checkEmpty($this->r, 'id');
      static::unpack($this->r);
      ProjMem::set(DbModelCore::cacheId($this->table, $this->r['id']), $this->r);
      if ($this->param != 'id') {
        DbModelCore::saveIdByParam($this->table, $this->param, $this->value, $this->r['id']);
      }
      $this->init();
    }
  }

  protected function getModelData() {
    return db()->selectRow("SELECT * FROM {$this->table} WHERE {$this->param}=?", $this->value);
  }

  protected function init() {
  }

  function save() {
    DbModelCore::update($this->table, $this->r['id'], $this->r);
  }

  protected $prop;

  function setProp($k, $v) {
    $this->prop[$k] = $v;
    return $this;
  }

  static function unpack(array &$r) {
    Err::noticeSwitch(false);
    foreach (static::$serializeble as $k) {
      $r[$k] = unserialize($r[$k]);
      if (!$r[$k]) $r[$k] = [];
    }
    Err::noticeSwitchBefore();
  }

  static function pack(array &$data) {
    if (empty(static::$serializeble)) return;
    foreach (static::$serializeble as $name) if (isset($data[$name])) $data[$name] = serialize($data[$name]);
  }

  static function addDefaultUpdateData(array &$data) {
    if (!static::$hasDefaultDateFields) return;
    if (empty($data['dateUpdate'])) $data['dateUpdate'] = Date::db();
  }

  static function update($table, $id, array $data, $filterByFields = false) {
    self::pack($data);
    self::addDefaultUpdateData($data);
    if ($filterByFields) $data = Arr::filterByKeys($data, db()->cols($table));
    db()->query("UPDATE $table SET ?a WHERE id=?", $data, $id);
    DbModelCore::cc($table, $id);
  }

  static function addDefaultCreateData(array &$data) {
    if (!static::$hasDefaultDateFields) return;
    if (empty($data['dateCreate'])) $data['dateCreate'] = Date::db();
    if (empty($data['dateUpdate'])) $data['dateUpdate'] = Date::db();
  }

  static function create($table, array $data, $filterByFields = false) {
    self::pack($data);
    self::addDefaultCreateData($data);
    Misc::checkEmpty($data);
    if ($filterByFields) $data = Arr::filterByKeys($data, db()->cols($table));
    return db()->query("INSERT INTO $table SET ?a", $data);
  }

}