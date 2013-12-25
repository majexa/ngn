<?php

abstract class TestFieldDdTagsTreeMultiselect extends TestFieldDdTagsAbstract {

  function test() {
    $tagId1 = DdTags::get('a', 'sample')->create(['title' => $this->v1]);
    $tagId2 = DdTags::get('a', 'sample')->create(['title' => $this->v2]);
    $id = static::$im->create(['sample' => $tagId1]);
    $this->a($this->v1, $tagId1, $id);
    static::$im->update($id, ['sample' => $tagId2]);
    $this->a($this->v2, $tagId2, $id);

  }

}