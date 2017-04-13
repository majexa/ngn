<?php

abstract class AbstractItems implements UpdatableItems, ArrayAccess {

  function offsetSet($offset, $value) {
    throw new Exception('not realized');
  }

  function offsetExists($offset) {
    return (bool)$this->offsetGet($offset);
  }

  function offsetUnset($offset) {
    throw new Exception('not realized');
  }

  protected $cache;

  function offsetGet($offset) {
    if (isset($this->cache[$offset])) return $this->cache[$offset];
    return $this->cache[$offset] = $this->getItem($offset);
  }

  abstract function updateField($id, $fieldName, $value);

  // Pagination

  /**
   * Есть ли постраничная выборка
   *
   * @var bool
   */
  public $hasPagination = false;

  /**
   * HTML код со ссылками на страницы
   *
   * @var string
   */
  public $pNums;

  /**
   * Общее кол-во записей не учитывая страничные лимиты
   *
   * @var integer
   */
  public $itemsTotal;

  /**
   * Всего страниц
   *
   * @var integer
   */
  public $pagesTotal;

  public $pNext;
  public $pPrev;

  function getPagination() {
    if (!$this->hasPagination) throw new Exception('Pagination was not enabled');
    if (!isset($this->itemsTotal)) throw new Exception('Use DbItems::prepareItemsConds() before calling getPagination()');
    return [
      'itemsTotal' => $this->itemsTotal,
      'pagesTotal' => $this->pagesTotal,
      'pNums'      => $this->pNums,
      'pNext'      => $this->pNext,
      'pPrev'      => $this->pPrev,
    ];
  }

}