<?php

class TestFieldDdItemSelect extends TestFieldDdItemSelectAbstract {

  protected $itemId1, $itemId2;

  function createTags() {
    $this->itemId1 = static::$im2->create(['title' => $this->v1]);
    $this->itemId2 = static::$im2->create(['title' => $this->v2]);
  }

  function createData() {
    return [static::$tagFieldName => $this->itemId1];
  }

  function runTests($request = false) {
    $this->runTagTest($this->itemId1, $this->v1);
    $this->updateItem([static::$tagFieldName => $this->itemId2], $request);
    $this->runTagTest($this->itemId2, $this->v2);
  }

  function runTagTest($tagId, $v) {
    $item = static::$im->items->getItemF($this->itemId);
    $this->assertTrue($item[static::$tagFieldName]['title'] == $v);
    $this->fillForm();
    $this->assertTrue((bool)strstr(static::$im->form->html(), '<option value="'.$tagId.'" selected>'.$v.'</option>'));
    $this->assertTrue((bool)strstr((new Ddo('a', 'siteItem'))->setItem($item)->els(), ">$v</div>"));
  }

}
