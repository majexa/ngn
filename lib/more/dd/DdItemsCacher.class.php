<?php

class DdItemsCacher {
use Options;

  /**
   * @var DdItemsPage
   */
  public $items;

  /**
   * @var DbModelPages
   */
  protected $page;

  /**
   * @var NgnCache
   */
  protected $cache;

  /**
   * @var DdoPage
   */
  public $ddo;

  protected $prefix;

  protected $ids;

  protected $html = [];

  static $force = true;

  static function getCache() {
    return NgnCache::c();
  }

  protected function defineOptions() {
    return ['cacheTags' => []];
  }

  function __construct(DdItems $items, Ddo $ddo, array $options = []) {
    $this->setOptions($options);
    $this->ddo = $ddo;
    $this->items = $items;
    $this->page = $this->items->page;
    $this->prefix = $items->table;
    $this->cache = self::getCache();
    $this->ids = $this->items->getItemIds();
  }

  function initHtml() {
    foreach ($this->ids as $id) {
      if (($r = $this->getHtml($id)) !== false) $this->html[$id] = $r;
      else
        $absent[] = $id;
    }
    if (isset($absent)) $this->saveHtml($absent);
    return $this;
  }

  protected function getHtml($id) {
    if (self::$force) return false;
    return $this->cache->load($this->prefix.$id);
  }

  protected function saveHtml(array $ids) {
    $this->items->cond->addF('id', $ids);
    $this->ddo->setItems($this->items->getItems());
    $html = $this->ddo->elsSeparate();
    foreach ($html as $id => $v) {
      $this->cache->save($v, $this->prefix.$id, $this->options['cacheTags']);
      $this->html[$id] = $v;
    }
    $this->html = Arr::sortByArray($this->html, $this->ids);
  }

  function html() {
    return $this->ddo->itemsBegin().implode('', $this->html).$this->ddo->itemsEnd();
  }

  static function cc($strName, $id) {
    self::getCache()->remove(DdCore::table($strName).$id);
  }

}

DdItemsCacher::$force = Config::getVarVar('dd', 'forceCache');
