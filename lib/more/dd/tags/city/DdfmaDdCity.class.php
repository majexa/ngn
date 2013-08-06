<?php

class DdfmaDdCity extends Ddfma {

  protected $proto, $old2newCountry = [];

  function updateCreate($name) {
    $region = include __DIR__.'/region.php';
    $city = include __DIR__.'/city.php';
    $largeCitys = array_flip(array_merge(
      File::lines(__DIR__.'/city1000'),
      File::lines(__DIR__.'/city500'),
      File::lines(__DIR__.'/city100')
    ));
    //$region = Arr::filterByValue($region, 'region_id', 3160);
    $this->proto = [
      'strName' => $this->strName,
      'groupName' => $name
    ];
    $this->createCountryTags();
    foreach ($region as $v) {
      $r = $this->proto;
      $r['title'] = $v['name'];
      $r['name'] = DdTags::title2name($v['name']);
      if (!$this->setRegionParentId($v, $r)) continue;
      if (isset($v['order'])) $r['oid'] = $v['order'];
      else $r['oid'] = 10000;
      $old2newRegion[$v['region_id']] = db()->insert('tags', $r);
    }
    $regionTagCount = [];
    foreach ($city as $v) {
      if (!isset($largeCitys[$v['name']])) continue;
      $r = $this->proto;
      $r['title'] = $v['name'];
      $r['name'] = DdTags::title2name($v['name']);
      if (!isset($old2newRegion[$v['region_id']])) continue;
      $r['parentId'] = $old2newRegion[$v['region_id']];
      //if (isset($v['order'])) $r['oid'] = $v['order'];
      $r['oid'] = $largeCitys[$v['name']];
      db()->insert('tags', $r);
      // Счетчик городов в регионах
      if (!isset($regionTagCount[$r['parentId']])) {
        $regionTagCount[$r['parentId']] = 1;
      } else {
        $regionTagCount[$r['parentId']]++;
      }
    }
    // Удаляем теги пустых регионов
    foreach ($old2newRegion as $tagId) {
      if (!isset($regionTagCount[$tagId]))
        db()->query("DELETE FROM tags WHERE id=$tagId");
    }
  }

  protected function setRegionParentId(array &$v, array &$r) {
    if (!isset($this->old2newCountry[$v['country_id']])) return false;
    $r['parentId'] = $this->old2newCountry[$v['country_id']];
    return true;
  }

  protected function createCountryTags() {
    $country = include __DIR__.'/country.php';
    foreach ($country as $v) {
      $r = $this->proto;
      $r['title'] = $v['name'];
      $r['name'] = DdTags::title2name($v['name']);
      if (isset($v['order'])) $r['oid'] = $v['order'];
      $this->old2newCountry[$v['country_id']] = db()->insert('tags', $r);
    }
  }

}
