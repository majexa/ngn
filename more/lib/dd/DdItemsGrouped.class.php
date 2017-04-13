<?php

class DdItemsGrouped {

  public $items;
  protected $groupedFieldName;

  function __construct($strName, $groupedFieldName, Db $db = null) {
    $this->groupedFieldName = $groupedFieldName;
    $this->items = new DdDbItemsExtended($strName, $db);
  }

  function getItems() {
    $items = $this->items->getItems_nocache();
    $r = [];
    foreach ($items as $item) {
      $gItem = $item[$this->groupedFieldName];
      if (!isset($r[$gItem['id']])) {
        $r[$gItem['id']] = [
          'item'     => $gItem,
          'subItems' => []
        ];
      }
      unset($item[$this->groupedFieldName]);
      $r[$gItem['id']]['subItems'][] = $item;
    }
    return $r;
  }

}