<?php

db();

class DbCond {

  protected $nullFilters = [];

  public $filters;

  public $filterCond = '';

  public $rangeFilterCond = '';

  public $range2rangeFilterCond = '';

  protected $filterMode = 'AND';

  public $orderCond;

  protected $orderKey;

  /**
   * @var bool
   */
  protected $orderAsc;

  public $limitCond;

  protected $tablePrefix;

  public $table;

  function __construct($table = null) {
    $this->table = $table;
    $this->tablePrefix = $table ? $table.'.' : '';
  }

  function all() {
    $conds = $this->getConditions();
    return $conds ? $this->getJoinCond()." WHERE 1\n".implode("\n", $conds)."\n" : '';
  }

  protected function _getConditions(array $except = []) {
    $conds = [];
    foreach (get_object_vars($this) as $k => $v) {
      if (!is_string($v) or !Misc::hasSuffix('Cond', $k)) continue;
      $name = Misc::removeSuffix('Cond', $k);
      if (in_array($name, $except)) continue;
      $conds[$name] = $v;
    }
    return $conds;
  }

  function getConditions(array $except = []) {
    $conds = $this->_getConditions($except);
    return array_merge(Arr::filterByExceptKeys($conds, ['limit', 'order']), Arr::filterByKeys($conds, [
        'limit',
        'order'
      ]) // эти должны быть в конце
    );
  }

  protected function getWhereConditions() {
    return Arr::filterByExceptKeys($this->_getConditions(), ['limit', 'order']);
  }

  function where() {
    return " WHERE 1\n".implode("\n", $this->getWhereConditions())."\n";
  }

  protected function getJoinCond() {
    if (!$this->table) return '';
    $r = '';
    foreach ($this->joins as $table => $v) $r .= "\nLEFT JOIN {$table} ON {$table}.{$v[0]}={$this->table}.$v[1]\n";
    return $r;
  }

  function removeFilter($key) {
    $this->_removeFilter('filter', $key);
    return $this;
  }

  function setFilterMode($mode = 'AND') {
    $this->filterMode = ($mode == 'AND') ? 'AND' : 'OR';
    return $this;
  }

  /**
   * @param   string  filterCond/itemFilterCond
   * @param   array   array(
   *                    'key' => 'asd',
   *                    'value' => 123,
   *                    'table' => 'tt',
   *                    'func' => ...
   *                  )
   */
  function addFilter(array $filter) {
    $this->_addFilter('filter', $filter);
    return $this;
  }

  function addF($key, $value, $func = null) {
    if (is_string($value) and strstr($value, ',')) $value = explode(',', $value);
    return $this->addFilter([
      'key'   => $key,
      'value' => $value,
      'func'  => $func
    ]);
  }

  protected function _addFilter($type, array $filter) {
    if (empty($filter['mode'])) $filter['mode'] = $this->filterMode;
    if (is_bool($filter['value'])) $filter['value'] = (int)$filter['value'];
    if (is_array($filter['value'])) {
      foreach ($filter['value'] as &$v) {
        if (!is_numeric($v)) $v = "'".mysql_real_escape_string($v, db()->link)."'";
      }
      $filter['value'] = implode(', ', $filter['value']);
    }
    else {
      if (!is_numeric($filter['value'])) $filter['value'] = "'".mysql_real_escape_string($filter['value'], db()->link)."'";
    }
    if (isset($this->filters[$type])) {
      foreach ($this->filters[$type] as $k => $f) {
        if ($f == $filter) $n = $k;
      }
      if (!isset($n)) $n = count($this->filters[$type]);
    } else {
      $n = 0;
    }
    $this->filters[$type][$n] = $filter;
    $this->setFiltersCond($type);
  }

  function _removeFilter($type, $key) {
    foreach ($this->filters[$type] as $n => $filter) {
      if ($filter['key'] == $key) $this->filters[$type] = Arr::dropN($this->filters[$type], $n);
    }
    $this->setFiltersCond($type);
    return $this;
  }

  function fromFilter($key, $from, $strict = false) {
    return $this->addRangeFilter($key, $from, false, null, $strict);
  }

  function toFilter($key, $to, $strict = false) {
    return $this->addRangeFilter($key, false, $to, null, $strict);
  }

  const strictBoth = 1, strictFrom = 2, strictTo = 3;

  /**
   * Добавляет фильтр по заданому диапозону значений
   *
   * @param   string  Имя поля таблицы
   * @param   string/bool   Значение начала диапозона. Если false, не учитывается
   * @param   string/bool   Значение конца диапозона. Если false, не учитывается
   * @param   string  Имя ф-ии, которую необходимо применить при вычислении значения диапозона
   * @param   bool    Строгое (>) или нестрогое (>=) неравенство
   */
  function addRangeFilter($key, $from, $to, $params = null, $strict = false) {
    $tablePrefix = $this->tablePrefix;
    $func = null;
    if ($params !== null) {
      if (isset($params['table'])) {
        $tablePrefix = $params['table'] === false ? '' : $params['table'].'.';
      }
      $func = isset($params['func']) ? $params['func'] : null;
    }
    if ($from !== false and !is_numeric($from)) $from = "'".mysql_real_escape_string($from)."'";
    if ($to !== false and !is_numeric($to)) $to = "'".mysql_real_escape_string($to)."'";
    $this->rangeFilterCond = $this->filterMode." ".
      ($from !== false ? ($func ? $func."(" : "").
        "$tablePrefix$key".($func ? ")" : "").
        (($strict == self::strictBoth or $strict == self::strictFrom) ? ' > ' : ' >= ').$from : '').
      ($to !== false ? (($from !== false ? ' AND ' : '').($func ? $func."(" : "")."$tablePrefix$key".($func ? ")" : "").(($strict == self::strictBoth or $strict == self::strictTo) ? ' < ' : ' <= ').$to) : '');
    return $this;
  }

  public $likeCond = '';

  function addLikeFilter($key, $text) {
    $this->likeCond = " AND $key LIKE '".mysql_real_escape_string($text)."'";
    return $this;
  }

  function addNullFilter($key, $isNull = false) {
    $this->filters['null'][$key] = $isNull;
    $this->setNullCond();
  }

  function addFromFilter($key, $from, $func = null, $strict = false) {
    $this->addRangeFilter($key, $from, false, $func, $strict);
  }

  function addToFilter($key, $to, $func = null, $strict = false) {
    $this->addRangeFilter($key, false, $to, $func, $strict);
  }

  protected function setFiltersCond($type) {
    $this->$type = ''; // Очищаем текущий фильтр
    $typeCond = $type.'Cond';
    $this->$typeCond = '';
    foreach ($this->filters[$type] as $v) {
      if (isset($v['table'])) {
        $tablePrefix = $v['table'] === null ? '' : $v['table'].'.';
      }
      else {
        $tablePrefix = $this->tablePrefix;
      }
      $this->$typeCond .= $v['mode']." ".(!empty($v['func']) ? $v['func']."(" : "")."$tablePrefix{$v['key']}".(!empty($v['func']) ? ")" : "")." IN (".$v['value'].")\n";
    }
    return $this;
  }

  protected function setNullCond() {
    $type = 'null';
    foreach ($this->nullFilters as $k => $isNull) {
      $typeCond = $type.'Cond';
      $this->$typeCond .= ' AND '.' '."{$this->tablePrefix}{$k}".($isNull ? ' = ' : ' != ')."''\n";
    }
  }

  function addRange2RangeFilter($keyBegin, $keyEnd, $from, $to, $func = null, $strict = false) {
    if (!is_numeric($from)) $from = "'".mysql_real_escape_string($from)."'";
    if (!is_numeric($to)) $to = "'".mysql_real_escape_string($to)."'";
    $this->range2rangeFilterCond = $this->filterMode." ".($func ? $func."(" : "")."{$this->tablePrefix}$keyEnd".($func ? ")" : "").($strict ? ' > ' : ' >= ').$from.' AND '.($func ? $func."(" : "")."{$this->tablePrefix}$keyBegin".($func ? ")" : "").($strict ? ' < ' : ' <= ').$to;
    return $this;
  }

  function setOrder($order = 'id DESC') {
    if (!$order) return;
    $this->orderKey = preg_replace('/(.*) (DESC|ASC)/', '$1', $order);
    $this->orderAsc = !strstr($order, 'DESC');
    $this->orderCond = "ORDER BY ".(strstr($order, '(') ? '' : $this->tablePrefix).$order;
    return $this;
  }

  function setLimit($limit) {
    if (!$limit) return $this;
    $this->limitCond = 'LIMIT '.mysql_real_escape_string($limit);
    return $this;
  }

  protected $joins = [];

  function addJoinF($table, $key, $value) {
    return $this->addJoinFilter([
      'table' => $table,
      'key'   => $key,
      'value' => $value
    ]);
  }

  function addJoinFilter(array $filter) {
    Arr::checkEmpty($filter, 'table');
    if (!isset($this->joins[$filter['table']])) throw new Exception("There is no join '{$filter['table']}'");
    $this->_addFilter('join', $filter);
    return $this;
  }

  function addJoin($foreiginTable, $id, $foreiginId = 'id') {
    if (isset($this->joins[$foreiginTable])) return;
    $this->joins[$foreiginTable] = [$foreiginId, $id];
    return $this;
  }

  static function get($table = null) {
    return new self($table);
  }

}
