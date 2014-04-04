<?php

abstract class TestFieldDdTagsTreeAbstract extends TestFieldDdTagsAbstract {

  protected $tagId1, $tagId2, $tagId3;

  function createTags() {
    $this->tagId1 = DdTags::get('a', 'sample')->create(['title' => $this->v1]);
    $this->tagId2 = DdTags::get('a', 'sample')->create([
      'title' => $this->v2,
      'parentId' => $this->tagId1
    ]);
    $this->tagId3 = DdTags::get('a', 'sample')->create([
      'title' => $this->v3,
      'parentId' => $this->tagId1
    ]);
  }

}