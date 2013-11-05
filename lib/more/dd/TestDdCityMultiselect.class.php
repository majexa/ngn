<?php

class TestDdCityMultiselect extends TestDd {

  function test() {
    (new DdFieldsManager('a'))->create([
      'title' => 'region',
      'name'  => 'region',
      'type'  => 'ddCityMultiselect'
    ]);
    $itemId = DdItemsManager::getDefault('a')->create(['region' => '951']);
    DdItemsManager::getDefault('a')->update($itemId, ['region' => '1267']);
    $this->assertTrue(count(DdItemsManager::getDefault('a')->items->getItem($itemId)['region']) == 1);
  }

}