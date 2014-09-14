<?php

abstract class TestFieldDdTagsFlatAbstract extends TestFieldDdTagsAbstract {

  protected $tagId1, $tagId2;

  function createTags() {
    $this->tagId1 = DdTags::get(static::$strName, static::$tagFieldName)->create(['title' => $this->v1]);
    $this->tagId2 = DdTags::get(static::$strName, static::$tagFieldName)->create(['title' => $this->v2]);
  }

}