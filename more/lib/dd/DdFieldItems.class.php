<?php

class DdFieldItems extends DbItems {

  function __construct($strName = null) {
    parent::__construct('dd_fields');
    if ($strName) $this->cond->addF('strName', $strName);
    $this->cond->setOrder('oid');
  }

  function getItems() {
    $items = parent::getItems();
    array_walk($items, function (&$v) {
      try {
        if (DdTagsGroup::getData($v['strName'], $v['name'], false)) {
          $v['tagGroup'] = (new DdTagsGroup($v['strName'], $v['name']))->p;
        }
      } catch (Exception $e) {
        throw new Exception("Problems with Id={$v['id']}, type={$v['type']}: ".$e->getMessage());
      }
    });
    return $items;
  }

}
