<?php

class TestFieldDdTagsMultiselect extends TestFieldDdTagsFlatAbstract {

  function createData() {
    return [static::$tagFieldName => [$this->tagId1]];
  }

  function runTests($request = false) {
    $this->a($this->v1, $this->tagId1);
    $this->updateItem([static::$tagFieldName => [$this->tagId2]], $request);
    $this->a($this->v2, $this->tagId2);
  }

  function a($v, $tagId) {
    $this->assertTrue(static::$im->items->getItem($this->itemId)[static::$tagFieldName][0]['title'] == $v);
    $item = static::$im->items->getItemF($this->itemId);
    $this->assertTrue($item[static::$tagFieldName][0]['title'] == $v);
    $this->fillForm();
    $this->assertTrue((bool)strstr(static::$im->form->html(), 'value="'.$tagId.'" checked>'));
    $this->assertTrue((bool)strstr((new Ddo('a', 'siteItem'))->setItem($item)->els(), '<div class="element f_sample t_ddTagsMultiselect">'.$v.'</div>'));
  }

}