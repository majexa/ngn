<?php

abstract class ItemsIterateAbstract extends ArrayAccesseble implements UpdatableItems {

  function __construct() {
    $this->r = $this->getItems();
  }

}