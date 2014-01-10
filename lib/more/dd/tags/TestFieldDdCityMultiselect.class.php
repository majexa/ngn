<?php

class TestFieldDdCityMultiselect extends TestDd {

  function test() {
    $id = static::$im->create(['sample' => [951]]);
    static::$im->update(['sample' => [1267]]);
    //DdItemsManager::getDefault('a')->update($itemId, ['region' => '1267']);
    //$this->assertTrue(count(DdItemsManager::getDefault('a')->items->getItem($itemId)['region']) == 1);
  }

}