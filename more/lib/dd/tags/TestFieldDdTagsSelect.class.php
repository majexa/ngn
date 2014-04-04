<?php

class TestFieldDdTagsSelect extends TestFieldDdTagsFlatAbstract {

  function createData() {
    return ['sample' => $this->tagId1];
  }

  function runTests($request = false) {
    $this->a($this->v1, $this->tagId1);
    $this->updateItem(['sample' => $this->tagId2], $request);
    $this->a($this->v2, $this->tagId2);
  }

  function a($v, $tagId) {
    $this->assertTrue(static::$im->items->getItem($this->itemId)['sample']['title'] == $v);
    $item = static::$im->items->getItemF($this->itemId);
    $this->assertTrue($item['sample']['title'] == $v);
    $this->fillForm();
    $this->assertTrue((bool)strstr(static::$im->form->html(), '<option value="'.$tagId.'" selected>'.$v.'</option>'));
    $this->assertTrue((bool)strstr((new Ddo('a', 'siteItem'))->setItem($item)->els(), '<div class="element f_sample t_ddTagsSelect">'.$v.'</div>'));
  }

}
