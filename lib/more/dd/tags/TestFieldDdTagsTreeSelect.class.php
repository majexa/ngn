<?php

class TestFieldDdTagsTreeSelect extends TestFieldDdTagsTreeAbstract {

  function a($tagId2, $v) {
    $item = static::$im->items->getItemF($this->itemId);
    $this->assertTrue($item['sample']['id'] == $this->tagId1);
    $this->assertTrue($item['sample']['childNodes'][0]['id'] == $tagId2);
    static::$im->requestUpdate($this->itemId);
    $this->formTest(static::$im->form->html(), $tagId2, $v);
    $this->ddoTest($item, $tagId2, $v);
  }

  function createItem() {
    return static::$im->create(['sample' => $this->tagId2]);
  }

  function updateItem() {
    static::$im->update($this->itemId, ['sample' => $this->tagId3]);
  }

  function formTest($html, $tagId, $v) {
    $this->assertTrue((bool)strstr($html, 'name="sample" value="'.$tagId.'" id="f_sample_'.$tagId.'" checked />'));
  }

  function ddoTest($item, $tagId, $v) {
    $this->assertTrue((bool)strstr((new Ddo('a', 'siteItem'))->setItem($item)->els(), '<span><a href="//t2.sample.'.$this->tagId1.'">'.$this->v1.'</a> → <a href="//t2.sample.'.$tagId.'">'.$v.'</a></span>'));
  }

}