<?php

class DbItems extends AbstractItems {
  use Options;

  public $table;

  /**
   * @var DbSite
   */
  public $db;

  /**
   * @var DbCond
   */
  public $cond;

  protected function defineOptions() {
    return [
      'db' => null,
      'paginationOptions' => []
    ];
  }

  function __construct($table, array $options = []) {
    $this->table = $table;
    $this->setOptions($options);
    $this->db = $this->options['db'] ?: db();
    $this->init();
  }

  protected function init() {
    $this->cond = new DbCond($this->table);
  }

  function __clone() {
    $this->init();
  }

  function count() {
    return $this->db->selectCell("SELECT COUNT(*) FROM {$this->table}".$this->cond->all());
  }

  function getItem($id) {
    return Arr::unserialize($this->db->selectRow("SELECT * FROM {$this->table} WHERE id=?d", $id));
  }

  function getItemF($id) {
    return $this->getItem($id);
  }

  function getItemByField($key, $val) {
    $cond = clone $this->cond;
    return Arr::unserialize($this->db->selectRow("SELECT * FROM {$this->table}".$cond->addF($key, $val)->all()));
  }

  function create(array $data) {
    return $this->___create($data);
  }

  function ___create(array $data) {
    if (empty($data['dateCreate'])) $data['dateCreate'] = Date::db();
    $data['dateUpdate'] = Date::db();
    if ($this->filterDataByColNames) $data = Arr::filterByKeys($data, db()->cols($this->table));
    return $this->db->query("INSERT INTO {$this->table} SET ?a", Arr::serialize($data));
  }

  function copy($id, $newData = null) {
    $row = db()->getRow($this->table, $id);
    unset($row['id']);
    unset($row['dateCreate']);
    if ($newData) $row = array_merge($row, $newData);
    return $this->___create($row);
  }

  public $filterDataByColNames = false;

  function update($id, array $data) {
    $data['dateUpdate'] = Date::db();
    if ($this->filterDataByColNames) $data = Arr::filterByKeys($data, db()->cols($this->table));
    $this->db->query("UPDATE {$this->table} SET ?a WHERE id=?d", Arr::serialize($data), $id);
  }

  function event($name, $id) {
  }

  function getItemNonFormat($id) {
    return $this->getItem($id);
  }

  function delete($id) {
    $this->db->query("DELETE FROM {$this->table} WHERE id=?d", $id);
  }

  function updateField($id, $k, $v) {
    if (is_array($v)) $v = serialize($v);
    $this->db->query("UPDATE {$this->table} SET $k=? WHERE id=?d", $v, $id);
  }

  // ----------------------- getting items ------------------------------

  function getItems() {
    $this->prepareItemsConds();
    $q = "
    SELECT
      {$this->table}.*,
      UNIX_TIMESTAMP({$this->table}.dateCreate) AS dateCreate_tStamp,
      UNIX_TIMESTAMP({$this->table}.dateUpdate) AS dateUpdate_tStamp,
      {$this->table}.id AS ARRAY_KEY
    FROM {$this->table}".$this->cond->all();
    $items = $this->db->query($q);
    foreach ($items as &$v) $v = Arr::unserialize($v);
    return $items;
  }

  function ids() {
    $this->prepareItemsConds();
    return $this->db->ids($this->table, $this->cond);
  }

  // ---------------------- items select conditions -----------------

  protected $selectCond;
  protected $filterSelectCond = '';

  protected $itemsCondsPrepared = false;

  function prepareItemsConds() {
    if ($this->itemsCondsPrepared) return $this;
    $this->_prepareItemsConds();
    $this->itemsCondsPrepared = true;
    return $this;
  }

  protected function _prepareItemsConds() {
    if ($this->hasPagination) {
      list($this->pNums, $offset, $this->itemsTotal, $this->pagesTotal, $this->pNext, $this->pPrev) = //
        (new Pagination($this->options['paginationOptions']))->get($this->table, $this->cond, $this->filterSelectCond);
      $this->cond->setLimit($offset);
    }
  }

  function addSelectCond($cond) {
    $this->selectCond .= ", $cond\n";
  }

  function addFilterSelectCond($cond) {
    $this->filterSelectCond .= ", $cond\n";
  }

  // ----------------- static -------------------

  static function createDummyTable($name) {
    db()->query(<<<SQL
CREATE TABLE IF NOT EXISTS $name (
  id int(11) NOT NULL AUTO_INCREMENT,
  active int(1) NOT NULL DEFAULT '1',
  dateCreate datetime DEFAULT NULL,
  dateUpdate datetime DEFAULT NULL,
  ip varchar(15) DEFAULT NULL,
  userId int(11) DEFAULT NULL,
  UNIQUE KEY id (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
SQL
    );
  }

  function addF($key, $value, $func = null) {
    $this->cond->addF($key, $value, $func);
    return $this;
  }

}