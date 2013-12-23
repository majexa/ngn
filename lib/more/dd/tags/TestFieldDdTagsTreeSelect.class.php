<?php

class TestFieldDdTagsTreeSelect extends TestFieldDdTagsBase {

  function test() {
    $tagId1 = DdTags::get('a', 'sample')->create(['title' => $this->v1]);
    $tagId2 = DdTags::get('a', 'sample')->create([
      'title' => $this->v2,
      'parentId' => $tagId1
    ]);
    $id = static::$im->create(['sample' => [$tagId2]]);
    die2(static::$im->items->getItemF($id));

    static::$im->requestUpdate($id);
    die2(static::$im->form->html());
    //die2(static::$im->items->getItem($id));
    //$this->a($this->v1, $tagId1, $id);
    //static::$im->update($id, ['sample' => [$tagId2]]);
    //$this->a($this->v2, $tagId2, $id);
  }

}