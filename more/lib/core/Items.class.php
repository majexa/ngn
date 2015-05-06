<?php


/**
 * реализация с комментариями и голосованиями. до того как они были вырезаны
 */
class Items extends DbItems {

  protected $id;

  function __construct($table, array $options = null) {
    parent::__construct($table, $options);
    $this->id = R::set('n', (int)R::get('n') + 1);
  }

  protected function setTable($table) {
    $this->table = $table;
  }

  function getItem($id) {
    return $this->getItem_nocache($id);
  }

  function getItemNonFormat($id) {
    return $this->getItem($id);
  }

  public $strict = false;

  function getItem_nocache($id) {
    $r = db()->selectRow("
      SELECT
        {$this->table}.*,
        UNIX_TIMESTAMP({$this->table}.dateCreate) AS dateCreate_tStamp,
        UNIX_TIMESTAMP({$this->table}.dateUpdate) AS dateUpdate_tStamp,
        users.id AS authorId,
        users.login AS authorLogin,
        users.name AS authorName
        {$this->selectCond}
      FROM {$this->table}
      LEFT JOIN users ON {$this->table}.userId=users.id
      WHERE
        {$this->table}.id=?d
        ", $id);
    if ($this->strict and !$r) throw new Exception("Item table={$this->table} with id={$id} does not exists");
    if (empty($r)) return false;
    else {
      try {
        $r = Arr::unserialize($r);
      } catch (Exception $e) {
        throw new Exception($e->getMessage()." (item ID=$id)");
      }
      return $r;
    }
  }

  protected function getItemCacheTags($id) {
    return ['item_'.$id];
  }

  function getItemF($id) {
    return $this->getItem($id);
  }

  function getItemByField($key, $val) {
    $this->setActiveCond();
    $r = db()->selectRow("
      SELECT
        {$this->table}.*,
        UNIX_TIMESTAMP({$this->table}.dateCreate) AS dateCreate_tStamp,
        UNIX_TIMESTAMP({$this->table}.dateUpdate) AS dateUpdate_tStamp,
        users.id AS authorId,
        users.login AS authorLogin,
        users.name AS authorName
        {$this->selectCond}
      FROM {$this->table}
      LEFT JOIN users ON {$this->table}.userId=users.id
      WHERE
        {$this->table}.$key=?
        ", $val);
    foreach ($r as &$v) {
      if (!empty($v) and Arr::unserializeble($v)) $v = unserialize($v);
    }
    return $r;
  }

  function setActiveCond() {
    $this->addF('active', 1);
  }

  function setN($n) {
    if (($n = (int)$n) != 0) $this->n = $n;
    return $this;
  }

  function setPagination($flag) {
    $this->isPagination = $flag;
    return $this;
  }

  /**
   * Получать неактивные записи тоже в любом случае
   *
   * @var bool
   */
  public $getNonActive = false;

  function getItems() {
    if (!($items = $this->getItems_nocache())) return [];
    //foreach ($items as $k => $item) foreach ($item as $kk => $v) if (!is_array($v) and Arr::unserializeble($v)) {
      //$items[$k][$kk] = unserialize($v);
    //}
    return $items;
  }

  function getItems_cache() {
    $cache = FileCache::c();
    if (($items = $cache->load($this->getCacheId())) === false) {
      $items = $this->getItems_nocache();
      if (!empty($items)) $cache->save($items, $this->getCacheId());
    }
    return $items;
  }

  protected $cacheId;

  function getCacheId() {
    $this->prepareItemsConds();
    return $this->table.md5(implode('', $this->cond->getConditions()));
  }

  function getItems_nocache() {
    $this->prepareItemsConds();
    $q = "
    SELECT SQL_CACHE
      {$this->table}.*,
      UNIX_TIMESTAMP({$this->table}.dateCreate) AS dateCreate_tStamp,
      UNIX_TIMESTAMP({$this->table}.dateUpdate) AS dateUpdate_tStamp,
      {$this->table}.id AS ARRAY_KEY,
      users.id AS authorId,
      users.login AS authorLogin,
      users.name AS authorName
      {$this->selectCond}
    FROM {$this->table}
    ".$this->cond->all();
    return db()->query($q);
  }

  function getMonths($fldName) {
    $months = [];
    $r = db()->select("SELECT YEAR($fldName) AS y, MONTH($fldName) AS m FROM {$this->table}
       WHERE 1 {$this->monthsFilterCond}");
    foreach ($r as $v) {
      if (!isset($months[$v['y']])) $months[$v['y']][] = $v['m'];
      elseif (!in_array($v['m'], $months[$v['y']])) {
        $months[$v['y']][] = $v['m'];
      }
    }
    return $months;
  }

  function create(array $data) {
    $data['ip'] = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
    $id = parent::create($data);
    return $id;
  }

  protected function cc($id) {
  }

  function update($id, array $data) {
    parent::update($id, $data);
    $this->cc($id);
  }

  function updateField($id, $k, $v) {
    parent::updateField($id, $k, $v);

    $this->cc($id);
  }

  function activate($id) {
    db()->query("UPDATE {$this->table} SET active=1 WHERE id=?d", $id);
    $this->cc($id);
  }

  function deactivate($id) {
    db()->query("UPDATE {$this->table} SET active=0 WHERE id=?d", $id);
    $this->cc($id);
  }

  function delete($id) {
    parent::delete($id);
    $this->cc($id);
  }

  function shiftUp($id) {
    DbShift::item($id, 'up', $this->table);
  }

  function shiftDown($id) {
    DbShift::item($id, 'down', $this->table);
  }

  protected function _prepareItemsConds() {
    $this->cond->addJoin('users', 'userId');
    if (!$this->getNonActive) $this->setActiveCond(); // $activeCond - должно стоять первым в запросе
    parent::_prepareItemsConds();
  }

  static function grid(array $d) {
    $d['body'] = array_values($d['body']);
    return $d;
  }

  ////////////// Events /////////////

  public $disableEvents = false;

  public $eventUserId = 0;

  function event($name, $id) {
    if ($this->disableEvents) return;
    $data = $this->getItemF($id);
    if (!isset($data['pageId'])) return;
    $data['itemId'] = $data['id'];
    if ($name == 'createItem') {
      ModerEventManager::event($data['pageId'], isset($this->eventUserId) ? $this->eventUserId : Auth::get('id'), $name, $data);
    }
    Events::create($data['pageId'], Auth::get('id'), $name, $data);
  }

}