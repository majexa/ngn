<?php

class DdItemSystemDatesManager extends DbItemsManager {

  function __construct($strName, $itemId) {
    parent::__construct(new DbItems(DdCore::table($strName)), new Form(new Fields([
      [
        'title' => 'Дата создания',
        'type' => 'datetime',
        'name' => 'dateCreate'
      ],
      [
        'title' => 'Дата изменения',
        'type' => 'datetime',
        'name' => 'dateUpdate'
      ]
    ])));
    $this->form->setElementsData($this->items->getItem($itemId));
  }

}