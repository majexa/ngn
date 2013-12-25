<?php

class TestFieldDdTagsSelect extends TestFieldDdTagsFlatAbstract {

  function createItem() {
    return static::$im->create(['sample' => $this->tagId1]);
  }

  function runTests() {
    $this->a($this->v1, $this->tagId1);
    static::$im->update($this->itemId, ['sample' => $this->tagId2]);
    $this->a($this->v2, $this->tagId2);
  }

  function a($v, $tagId) {
    //prr();
    die2(static::$im->items->getItem($this->itemId));
    $this->assertTrue(static::$im->items->getItem($this->itemId)['sample']['title'] == $v);
    $item = static::$im->items->getItemF($this->itemId);
    $this->assertTrue($item['sample']['title'] == $v);
    static::$im->requestUpdate($this->itemId);
    $this->assertTrue((bool)strstr(static::$im->form->html(), '<option value="'.$tagId.'" selected>'.$v.'</option>'));
    $this->assertTrue((bool)strstr((new Ddo('a', 'siteItem'))->setItem($item)->els(), '<div class="element f_sample t_ddTagsSelect">'.$v.'</div>'));
  }

}
