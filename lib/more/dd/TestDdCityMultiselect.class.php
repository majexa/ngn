<?php

class TestDdCityMultiselect extends TestDd {

  function test() {
    //self::$sm->deleteByName('a');
    (new DdFieldsManager('a'))->create([
      'title' => 'region',
      'name'  => 'region',
      'type'  => 'ddCityMultiselect'
    ]);
    $itemId = (new DdItems('a'))->create([]);
    db()->query(<<<SQL
INSERT INTO tagItems (groupName, strName, tagId, itemId, collection, active) VALUES
('region', 'a', 1451, $itemId, 33, 1),
('region', 'a', 300, $itemId, 33, 1),
('region', 'a', 200, $itemId, 33, 1),
('region', 'a', 31, $itemId, 33, 1),
('region', 'a', 1451, $itemId, $itemId, 1),
('region', 'a', 300, $itemId, 33, 1),
('region', 'a', 200, $itemId, 33, 1),
('region', 'a', 31, $itemId, 33, 1),
('region', 'a', 1452, $itemId, 34, 1),
('region', 'a', 300, $itemId, 35, 1),
('region', 'a', 200, $itemId, 35, 1),
('region', 'a', 31, $itemId, 35, 1),
('region', 'a', 1452, $itemId, 34, 1),
('region', 'a', 300, $itemId, 35, 1),
('region', 'a', 200, $itemId, 35, 1),
('region', 'a', 31, $itemId, 35, 1),
('region', 'a', 1453, $itemId, 35, 1),
('region', 'a', 300, $itemId, 36, 1),
('region', 'a', 200, $itemId, 36, 1),
('region', 'a', 31, $itemId, 36, 1);
('region', 'a', 1453, $itemId, 35, 1),
('region', 'a', 300, $itemId, 36, 1),
('region', 'a', 200, $itemId, 36, 1),
('region', 'a', 31, $itemId, 36, 1),
('region', 'a', 3254, $itemId, 29, 1),
('region', 'a', 300, $itemId, 30, 1),
('region', 'a', 200, $itemId, 30, 1),
('region', 'a', 31, $itemId, 30, 1),
('region', 'a', 3254, $itemId, 29, 1),
('region', 'a', 300, $itemId, 30, 1),
('region', 'a', 200, $itemId, 30, 1),
('region', 'a', 31, $itemId, 30, 1),
('region', 'a', 3255, $itemId, 30, 1),
('region', 'a', 300, $itemId, 31, 1),
('region', 'a', 200, $itemId, 31, 1),
('region', 'a', 31, $itemId, 31, 1),
('region', 'a', 3255, $itemId, 30, 1),
('region', 'a', 300, $itemId, 31, 1),
('region', 'a', 200, $itemId, 31, 1),
('region', 'a', 31, $itemId, 31, 1),
('region', 'a', 3256, $itemId, 31, 1),
('region', 'a', 300, $itemId, $itemId, 1),
('region', 'a', 200, $itemId, $itemId, 1),
('region', 'a', 31, $itemId, $itemId, 1),
('region', 'a', 3256, $itemId, 31, 1),
('region', 'a', 300, $itemId, $itemId, 1),
('region', 'a', 200, $itemId, $itemId, 1),
('region', 'a', 31, $itemId, $itemId, 1),
('region', 'a', 3257, $itemId, 33, 1),
('region', 'a', 300, $itemId, 34, 1),
('region', 'a', 200, $itemId, 34, 1),
('region', 'a', 31, $itemId, 34, 1),
('region', 'a', 3257, $itemId, 33, 1),
('region', 'a', 300, $itemId, 34, 1),
('region', 'a', 200, $itemId, 34, 1),
('region', 'a', 31, $itemId, 34, 1),
SQL
  );
    prr(DdTags::items('a', 'region')->getItems($itemId));
  }

  static function tearDownAfterClass() {}

}