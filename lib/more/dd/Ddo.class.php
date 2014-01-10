<?php

class Ddo {
  use Options;

  protected $debug = false;

  /**
   * Массив всех полей, присутствующих в этих записях
   *
   * @var a
   */
  public $fields;

  /**
   * Массив всех записей
   *
   * @var array
   */
  public $items;

  public $ddddItemLink;

  /**
   * Флаг определяет выводится ли список записей или одна запись
   *
   * @var bool
   */
  protected $list;

  public $layoutName;

  /**
   * @var DdoSettings
   */
  protected $settings;

  /**
   * @var string
   */
  public $strName;

  public $titled = false, $text = false, $even = false, $titledSettings;

  function __construct($strName, $layoutName, array $options = []) {
    $this->setOptions($options);
    $this->strName = $strName;
    $this->layoutName = $layoutName;
    $this->settings = $this->getSettings();
    $this->titledSettings = $this->settings->getDataLayout('titled', $layoutName);
  }

  protected function getSettings() {
    return new DdoSettings($this->strName);
  }

  protected function getPagePath() {
    return isset($this->pagePath) ? $this->pagePath : '';
  }

  public $pagePath;

  function setPagePath($pagePath) {
    $this->pagePath = $pagePath;
    return $this;
  }

  function setDebug($debug) {
    $this->debug = $debug;
  }

  function initFields() {
    if (isset($this->fields)) return $this;
    $fields = O::gett('DdoFields', $this->settings, $this->layoutName, $this->strName, empty($this->options['fieldOptions']) ? [] : $this->options['fieldOptions']);
    $fields->isItemsList = $this->list;
    $this->fields = $fields->getFields();
    return $this;
  }

  function setItem($item) {
    $this->list = false;
    $this->items = [$item['id'] => $item];
    $this->init();
    return $this;
  }

  function setItems($items) {
    $this->list = true;
    unset($this->items);
    $this->items = $items;
    $this->init();
    return $this;
  }

  protected function init() {
    $this->initFields();
    $this->initTpls();
    $this->initOutputMethodsTpls();
  }

  public $ddddByType;
  public $ssssByType;
  public $tplPathByType;

  protected function initTpls() {
    foreach (['ddddByType' /*, 'ssssByType', 'tplPathByType'*/] as $type) {
      $r = Config::getVar("ddo/$type.default");
      // if (($r2 = Config::getVar("ddo/$type.default.{$this->layoutName}", true)) !== false) $r = array_merge($r, $r2);
      if (($r2 = Config::getVar("ddo/$type", true)) !== false) $r = array_merge($r, $r2);
      if ($this->list and ($r2 = Config::getVar("ddo/$type.list", true)) !== false) {
        $r = array_merge($r, $r2);
      }
      foreach ($r as &$v) if (strlen($v) and $v[0] == '%') {
        $k = ltrim($v, '%');
        if (!isset($r[$k])) throw new Exception("Type '$type :: $k' does not exists");
        $v = $r[$k];
      }
      $this->$type = $r;
    }
    if (($r = Config::getVar("ddo/ddddByName", true)) !== false) $this->ddddByName = array_merge($this->ddddByName, $r);
  }

  public $ddddByName = [];
  public $tplPathByName = [];

  protected function initOutputMethodsTpls() {
    $outputMethod = $this->settings->getOutputMethod();
    if (!isset($outputMethod[$this->layoutName])) return;
    foreach ($outputMethod[$this->layoutName] as $fieldName => $method) {
      if (empty($this->fields[$fieldName])) {
        // Если output-метдо существует, но поле не должно выводиться
        continue;
      }
      $methods = DdoMethods::getInstance();
      $fieldType = $this->fields[$fieldName]['type'];
      if (isset($methods->field[$fieldType][$method]['dddd'])) {
        $this->ddddByName[$fieldName] = $methods->field[$fieldType][$method]['dddd'];
      }
      elseif (isset($methods->field[$fieldType][$method]['tpl'])) {
        $this->tplPathByName[$fieldName] = $methods->field[$fieldType][$method]['tpl'];
      }
    }
  }

  public $ddddDefault = '$v ? $v : ``';

  protected function _htmlEl($data) {
    $data['ddddItemLink'] = St::dddd($data['ddddItemLink'], $data);
    $ddddByType = array_merge($this->ddddByType, self::$_ddddByType);
    $ddddByName = array_merge($this->ddddByName, self::$_ddddByName);
    if (isset(self::$funcByName[$data['name']])) {
      $func = self::$funcByName[$data['name']];
      //try {
      $r = ($this->debug ? 'funcByName:'.$data['name'].'=' : ''). // debug
        $func($data);
      //} catch (Exception $e) {
//        throw new Exception('funcByName name="'.$data['name'].'" error: '.$e->getMessage());
      //    }
      return $r;
    }
    elseif (isset($ddddByName[$data['name']])) {
      $r = ($this->debug ? 'ddddByName:'.$data['name'].'=' : '').St::dddd($ddddByName[$data['name']], $data);
      //try {
      //  $r = ($this->debug ? 'ddddByName:'.$data['name'].'=' : '').St::dddd($ddddByName[$data['name']], $data);
      //} catch (Exception $e) {
      //  throw new Exception('ddddByName name="'.$data['name'].'" error: '.$e->getMessage());
      //}
      return $r;
    }
    elseif (isset($this->d[$data['type']])) {
    }
    elseif (isset($this->tplPathByName[$data['name']])) {
      return ($this->debug ? 'tplPathByName:name:'.$data['name'] : ''). // debug
      Tt()->getTpl($this->tplPathByName[$data['name']], $data);
    }
    elseif (isset($this->tplPathByType[$data['type']])) {
      return ($this->debug ? 'tplPathByType:type:'.$this->tplPathByType[$data['type']].'=' : ''). // debug
      Tt()->getTpl($this->tplPathByType[$data['type']], $data);
    }
    elseif (isset($this->ssssByType[$data['type']])) {
      try {
        $r = ($this->debug ? 'ssssByType:'.$data['type'].'=' : ''). // debug
          St::ssss($this->ssssByType[$data['type']], $data);
      } catch (Exception $e) {
        throw new DdoException($e, 'ssssByType type="'.$data['type'].', name="'.$data['name'].'", current class='.get_class($this).'". error: '.$e->getMessage());
      }
      return $r;
    }
    elseif (isset($ddddByType[$data['type']])) {
      //try {
        $r = ($this->debug ? 'ddddByType:'.$data['type'].'=' : ''). // debug
          St::dddd($ddddByType[$data['type']], $data);
      //} catch (Exception $e) {
      //  throw new DdoException($e, 'ddddByType type="'.$data['type'].', name="'.$data['name'].'", itemId='.$data['id'].' current class='.get_class($this).'". error: '.$e->getMessage());
      //}
      return $r;
    }
    else {
      if (is_array($data['v'])) {
        throw new Exception('Type "'.$data['type'].'" of field "'.$data['name'].'" has no DDO. Value for default output can not be an array');
      }
      return ($this->debug ? 'ddddDefault (type='.$data['type'].'): ' : ''). // debug
      St::dddd($this->ddddDefault, $data);
    }
  }

  protected function htmlEl(array $data) {
    $html = $this->_htmlEl($data);
    if ($html and $this->titled or !empty($this->titledSettings[$data['name']])) $html = '<b class="title">'.$data['title'].':</b> '.$html;
    if ($html and $this->text) $html = strip_tags($html)."\n";
    return $html;
  }

  protected function check() {
    if (!isset($this->fields)) throw new Exception('$this->fields not defined. Use setItem() or setItems() before');
  }

  // ------------- Element -------------- 

  /**
   * Возвращает HTML элемента DD-записи
   *
   * @param   array Значение элемента записи
   * @param   array Имя поля
   * @param   array   ID записи
   * @return  string  HTML
   */
  function el($value, $fieldName, $itemId) {
    $this->check();
    if ($itemId) { // Если $itemId != null
      $item = $this->items[$itemId];
      if (!isset($item)) throw new Exception("No data for item ID=$itemId. Items: ".getPrr($this->items));
    }
    if (isset($item[$fieldName.'_f'])) $value = $item[$fieldName.'_f'];
    if (empty($this->fields[$fieldName])) throw new Exception("No field for field name=$fieldName. Fields:".getPrr($this->fields));
    $f = $this->fields[$fieldName];
    if (!isset($f['type'])) $f['type'] = 'text';
    $tplData = [
      'pagePath'     => $this->getPagePath(),
      'id'           => $itemId,
      'f'            => $f,
      'type'         => $f['type'],
      'title'        => isset($f['title']) ? $f['title'] : '',
      'name'         => $f['name'],
      'ddddItemLink' => $this->ddddItemLink,
      'authorId'     => $item['authorId'],
      'userGroupId'  => $item['userGroupId'],
      'v'            => $value,
      'o'            => $this
    ];
    if (FieldCore::hasAncestor($f['type'], 'file')) {
      if (isset($item[$fieldName.'_fSize'])) $tplData['fSize'] = $item[$fieldName.'_fSize'];
    }
    return ($this->debug ? "\n\n<!-- Field=$fieldName, Value=".(is_scalar($value) ? $value : '['.gettype($value).']').". Current DdoPage class: ".get_class($this)." -->\n\n" : '').$this->htmlEl($tplData);
  }

  // ------------- Elements -------------- 

  function hgrpBeginDddd($type, $name, $evenNum) {
    ($this->even === true) ? $even = ' even_'.$evenNum : $even = '';
    return '<!-- Open fields group --><div class="hgrp hgrpt_'.$type.' hgrp_'.$name.' '.$even.'">';
  }

  public $ddddItemsBegin = '`<div class="items ddItems str_`.$strName.`">`';
  public $tplPathItem = 'dd/elements';
  public $ddddItemsEnd = '`</div><!-- Конец цикла вывода записей по списку полей структуры "`.$strName.`" -->`';
  public $premoder = false;
  //public $hgrpBeginDddd = '`<!-- Open fields group --><div class="hgrp hgrpt_`.$type.` hgrp_`.$name.` even_`.$evenNum.`">`';
  public $elBeginDddd = '`<div class="element f_`.$name.` t_`.$type.`">`';
  public $elEnd = '</div>';

  function itemsBegin() {
    if ($this->text) return '';
    return St::dddd($this->ddddItemsBegin, ['strName' => $this->strName]);
  }

  function itemsEnd() {
    if ($this->text) return '';
    return St::dddd($this->ddddItemsEnd, ['strName' => $this->strName]);
  }

  public $textItemSeparator = "--\n";

  protected $excelWriters = [];

  /**
   * @param $file
   * @return ExcelWriter
   */
  protected function getExcelWriter($file) {
    if (isset($this->excelWriter[$file])) return $this->excelWriter[$file];
    $this->excelWriter[$file] = new ExcelWriter($file);
    return $this->excelWriter[$file];
  }

  function els() {
    Err::noticeSwitch(false);
    $this->check();
    if ($this->debug) print 'class='.get_class($this);
    if ($this->text) {
      $text = '';
      foreach ($this->items as $v) {
        foreach ($this->fields as $f) {
          if (isset($v[$f['name']])) $text .= $this->el($v[$f['name']], $f['name'], $v['id']);
        }
        $text .= $this->textItemSeparator;
      }
      return $text;
    }
    $html = '<!-- Начало цикла вывода записей по списку элементов структуры "'.$this->strName.'" -->'."\n";
    $html .= $this->itemsBegin();
    foreach ($this->items as $v) $html .= $this->elsItem($v);
    $html .= $this->itemsEnd();
    Err::noticeSwitchBefore();
    return $html;
  }

  function table() {
    $this->check();
    $this->text = true;
    $this->titled = false;
    $rows[] = Arr::get($this->fields, 'title');
    foreach ($this->items as $v) {
      $row = [];
      foreach ($this->fields as $f) {
        if (isset($v[$f['name']])) $row[] = $this->el($v[$f['name']], $f['name'], $v['id']);
        else $row[] = '';
      }
      $rows[] = $row;
    }
    return Tt()->getTpl('common/table', $rows);
  }

  function xls($file, $header = true) {
    Err::noticeSwitch(false);
    $this->check();
    $this->text = true;
    $this->titled = false;
    $exl = $this->getExcelWriter($file);
    if ($header) $exl->writeLine(Arr::get($this->fields, 'title'));
    $rows = [];
    foreach ($this->items as $v) {
      $row = [];
      foreach ($this->fields as $f) {
        if (isset($v[$f['name']])) $row[] = $this->el($v[$f['name']], $f['name'], $v['id']);
        else $row[] = '';
      }
      $exl->writeLine($row);
      $rows[] = $row;
    }
    Err::noticeSwitchBefore();
  }

  function elsSeparate() {
    Err::noticeSwitch(false);
    $html = [];
    foreach ($this->items as $v) $html[$v['id']] = $this->elsItem($v);
    Err::noticeSwitchBefore();
    return $html;
  }

  protected function elsItem(&$item) {
    $item['o'] = $this;
    $v['premoder'] = $this->premoder;
    return Tt()->getTpl($this->tplPathItem, $item);
  }

  static function getFlatValue($v) {
    if (is_array($v)) if (isset($v['name'])) return $v['name'];
    else return $v;
  }

  static protected $_ddddByType = [];

  static function addDdddByType($type, $dddd) {
    self::$_ddddByType[$type] = $dddd;
  }

  static protected $_ddddByName = [];

  static function addDdddByName($name, $dddd) {
    self::$_ddddByName[$name] = $dddd;
  }

  static $funcByName = [];

  static function addFuncByName($name, Closure $func) {
    self::$funcByName[$name] = $func;
  }

  // global space for something
  static $g;

  // space for some instance data
  public $d;

  // -------- grid --------

  static function getGrid($items, Ddo $ddo) {
    $grid['head'] = Arr::get(array_map(function ($v) {
      if (DdFieldCore::isBoolType($v['type'])) $v['title'] = '';
      return $v;
    }, $ddo->initFields()->fields), 'title');
    $grid['fieldNames'] = Arr::get($ddo->initFields()->fields, 'name');
    $ddo->setItems($items);
    $grid['body'] = array_map(function ($v) use ($ddo) {
      return Ddo::gridRowPrepare($v, $ddo);
    }, array_values($items));
    return $grid;
  }

  static function gridRowPrepare($item, Ddo $ddo, array $opt = []) {
    $r = Arr::filterByKeys($item, ['id', 'active']);
    $r['tools'] = [
      'delete' => 'Удалить',
      'edit'   => 'Редактировать'
    ];
    $r['tools']['active'] = [
      'type' => 'switcher',
      'on'   => $item['active']
    ];
    if (!empty($opt['canMove'])) $r['tools']['move'] = LANG_MOVE;
    if (Config::getVarVar('dd', 'allowEditSystemDates', true)) $r['tools']['editDate'] = 'Редактировать системные даты';
    foreach ($ddo->fields as $f) {
      if (array_key_exists($f['name'], $item)) {
        $r['data'][$f['name']] = $ddo->el($item[$f['name']], $f['name'], $item['id']);
      }
      elseif (!empty($f['forceEmpty'])) {
        $r['data'][$f['name']] = $ddo->el('dummy', $f['name'], $item['id']);
      }
      else {
        $r['data'][$f['name']] = '';
      }
    }
    if (($paths = Hook::paths('dd/gridRowPrepare')) !== false) foreach ($paths as $path) include $path;
    return $r;
  }

}