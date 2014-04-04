<?php

abstract class TestFieldDdTagsFlatAbstract extends TestFieldDdTagsAbstract {

  protected $tagId1, $tagId2;

  function createTags() {
    $this->tagId1 = DdTags::get('a', 'sample')->create(['title' => $this->v1]);
    $this->tagId2 = DdTags::get('a', 'sample')->create(['title' => $this->v2]);
  }

}