<?php

class TestFilterStr extends NgnTestCase {

  function test() {
    $sm = new DdStructuresManager();
    $id1 = $sm->create([
      'title' => 'a',
      'name' => 'a'
    ]);
    $id2 = $sm->create([
      'title' => 'b',
      'name' => 'b',
      'filterStrName' => 'a'
    ]);
    $bfm = new DdFieldsManager('b');
    $bfm->create([
      'title' => 'tag',
      'name' => 'catalog',
      'type' => 'ddTagsSelect'
    ]);
    $tags = DdTags::get('b', 'catalog');
    $tags->create(['title' => 'one']);
    $tags->create(['title' => 'two']);
    DdTagsItems::create('b', 'catalog', 1, ['one'], true);
    DdTagsItems::create('a', 'catalog', 1, ['one', 'two'], true);
    $this->assertNotEmpty(db()->selectCell("select * from tags_items WHERE strName='a' AND groupName='catalog'"));
    $bfm->deleteByName('catalog');
    $this->assertEmpty(db()->selectCell("select * from tags_items WHERE strName='a' AND groupName='catalog'"));
    $sm->delete($id1);
    $sm->delete($id2);
  }

}