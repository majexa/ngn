<?php

class TestFieldDdItemsSelect extends TestFieldDdItemSelect {

  protected $itemId3;

  function createTags() {
    parent::createTags();
    $this->itemId3 = static::$im2->create(['title' => $this->v3]);
  }

  function createData() {
    return [static::$tagFieldName => [$this->itemId1, $this->itemId2]];
  }

  function runTests($request = false) {
    $this->runTagTest($this->itemId2, $this->v2);
    $this->updateItem([static::$tagFieldName => [$this->itemId1, $this->itemId3]], $request);
    $this->runTagTest($this->itemId3, $this->v3);
  }

  function runTagTest($tagId, $v) {
    $item = static::$im->items->getItemF($this->itemId);
    $tagItems = new DdTagsItems('a', 'sample');
    $tagItems->getRelatedItems = true;
    if (empty($item[static::$tagFieldName][0]['title'])) die2([static::$tagFieldName, $item]);
    $this->assertTrue($item[static::$tagFieldName][0]['title'] == $this->v1);
    $this->assertTrue($item[static::$tagFieldName][1]['title'] == $v);
    $this->fillForm();
    $this->assertTrue((bool)strstr(static::$im->form->html(), ' value="'.$this->itemId1.'" checked>'));
    $this->assertTrue((bool)strstr(static::$im->form->html(), ' value="'.$tagId.'" checked>'));
    $this->assertTrue((bool)strstr((new Ddo('a', 'siteItem'))->setItem($item)->els(), '>'.$this->v1.', '.$v.'</div>'));
  }

}