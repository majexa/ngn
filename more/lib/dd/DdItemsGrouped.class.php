<?php

class DdItemsGrouped extends DdItems {

  protected $groupedFieldName;

  function __construct($strName, $groupedFieldName, Db $db = null) {
    $this->groupedFieldName = $groupedFieldName;
    parent::__construct($strName, $db);
  }

  function getItems() {
    $items = parent::getItems();
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