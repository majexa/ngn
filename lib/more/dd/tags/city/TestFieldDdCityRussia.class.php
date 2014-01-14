<?php

abstract class TestFieldDdCityRussia extends TestFieldDdTagsFlatAbstract {

  protected $tagId1, $tagId2;

  function runTests() {
    $this->v1 = 'Московская область';
    $this->v2 = 'Москва';
    $this->tagId1 = 200;
    $this->tagId2 = 779;
  }

  function createTags() {
    $this->tagId1 = DdTags::get('a', 'sample')->create(['title' => $this->v1]);
    $this->tagId2 = DdTags::get('a', 'sample')->create(['title' => $this->v2]);
  }
  //function test()

}