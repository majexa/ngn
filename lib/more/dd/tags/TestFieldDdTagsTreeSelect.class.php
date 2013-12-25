<?php

class TestFieldDdTagsTreeSelect extends TestFieldDdTagsTreeAbstract {

  function a($tagId2, $v) {
    $item = static::$im->items->getItemF($this->itemId);
    $this->assertTrue($item['sample']['id'] == $this->tagId1);
    $this->assertTrue($item['sample']['childNodes'][0]['id'] == $tagId2);
    static::$im->requestUpdate($this->itemId);
    $this->assertTrue((bool)strstr(static::$im->form->html(), 'name="sample" value="'.$tagId2.'" id="f_sample_'.$tagId2.'" checked />'));
    $this->assertTrue((bool)strstr((new Ddo('a', 'siteItem'))->setItem($item)->els(), '<span><a href="//t2.sample.'.$this->tagId1.'">'.$this->v1.'</a> → <a href="//t2.sample.'.$tagId2.'">'.$v.'</a></span>'));
  }

}