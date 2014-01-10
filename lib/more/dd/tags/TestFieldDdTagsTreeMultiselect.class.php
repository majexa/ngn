<?php

class TestFieldDdTagsTreeMultiselect extends TestFieldDdTagsTreeAbstract {

  protected $tagId4, $v4 = 'four';

  function createTags() {
    parent::createTags();
    $this->tagId4 = DdTags::get('a', 'sample')->create([
      'title' => $this->v4,
      'parentId' => $this->tagId1
    ]);
  }

  function createItem() {
    return static::$im->create(['sample' => [$this->tagId2, $this->tagId3]]);
  }

  function updateItem() {
    static::$im->update($this->itemId, ['sample' => [$this->tagId3]]);
  }

  function a($tagId, $v) {
    static::$im->requestUpdate($this->itemId);
    $html = static::$im->form->html();
    $this->assertTrue((bool)strstr($html, 'id="sample_'.$tagId.'" checked />'));
    $this->assertTrue((bool)strstr($html, 'id="sample_'.$tagId.'" checked />'));
  }

  function ddoTest($item, $tagId, $v) {}

}