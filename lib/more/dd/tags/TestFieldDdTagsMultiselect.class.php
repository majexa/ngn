<?php

class TestFieldDdTagsMultiselect extends TestFieldDdTagsBase {

  function a($v, $tagId, $id) {
    $this->assertTrue(static::$im->items->getItem($id)['sample'][0]['title'] == $v);
    $item = static::$im->items->getItemF($id);
    $this->assertTrue($item['sample'][0]['title'] == $v);
    static::$im->requestUpdate($id);
    $this->assertTrue((bool)strstr(static::$im->form->html(), 'value="'.$tagId.'" checked>'));
    $this->assertTrue((bool)strstr((new Ddo('a', 'siteItem'))->setItem($item)->els(), '<div class="element f_sample t_ddTagsMultiselect">'.$v.'</div>'));
  }

  function test() {
    $tagId1 = DdTags::get('a', 'sample')->create(['title' => $this->v1]);
    $tagId2 = DdTags::get('a', 'sample')->create(['title' => $this->v2]);
    $id = static::$im->create(['sample' => [$tagId1]]);
    $this->a($this->v1, $tagId1, $id);
    static::$im->update($id, ['sample' => [$tagId2]]);
    $this->a($this->v2, $tagId2, $id);
  }

}