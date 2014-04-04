<?php

class TestFieldDdTagsMultiselect extends TestFieldDdTagsFlatAbstract {

  function createData() {
    return ['sample' => [$this->tagId1]];
  }

  function runTests($request = false) {
    $this->a($this->v1, $this->tagId1);
    $this->updateItem(['sample' => [$this->tagId2]], $request);
    $this->a($this->v2, $this->tagId2);
  }

  function a($v, $tagId) {
    $this->assertTrue(static::$im->items->getItem($this->itemId)['sample'][0]['title'] == $v);
    $item = static::$im->items->getItemF($this->itemId);
    $this->assertTrue($item['sample'][0]['title'] == $v);
    $this->fillForm();
    $this->assertTrue((bool)strstr(static::$im->form->html(), 'value="'.$tagId.'" checked>'));
    $this->assertTrue((bool)strstr((new Ddo('a', 'siteItem'))->setItem($item)->els(), '<div class="element f_sample t_ddTagsMultiselect">'.$v.'</div>'));
  }

}