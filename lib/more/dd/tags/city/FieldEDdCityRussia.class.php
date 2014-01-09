<?php

DdFieldCore::registerType('ddCityRussia', [
  'dbType'   => 'VARCHAR',
  'dbLength' => 255,
  'title'    => 'Город России',
  'order'    => 292,
  'tags'     => true,
  'tagsTree' => true
]);

class FieldEDdCityRussia extends FieldEDdCity {

  protected $rootTagId = 300;

  protected function getRootOptions() {
    $this->tags->getSelectCond()->setOrder('title');
    $tags = $this->tags->getTags($this->rootTagId);
    $_tags = $this->tags->getTags(Arr::get($tags, 'id'));
    $tags = [];
    $topIds = [50, 779, 780, 47]; // Московкая обл, Москва, Питер, Лен.обл.
    foreach ($_tags as $v) if (in_array($v['id'], $topIds)) $tags[] = $v;
    $tags = Arr::sortByOrderKey($tags, 'oid');
    foreach ($_tags as $v) if (!in_array($v['id'], $topIds)) $tags[] = $v;
    return ['' => '—'] + Arr::get($tags, 'title', 'id');
  }

  protected function firstN() {
    return 2;
  }

}