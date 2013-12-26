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

  function formTest($html, $tagId2, $v) {
    $this->assertTrue((bool)strstr($html, 'name="sample" value="'.$tagId2.'" id="f_sample_'.$tagId2.'" checked />'));
  }

  function ddoTest($item, $tagId2, $v) {
    $this->assertTrue((bool)strstr((new Ddo('a', 'siteItem'))->setItem($item)->els(), '<span><a href="//t2.sample.'.$this->tagId1.'">'.$this->v1.'</a> → <a href="//t2.sample.'.$tagId2.'">'.$v.'</a></span>'));
  }

}