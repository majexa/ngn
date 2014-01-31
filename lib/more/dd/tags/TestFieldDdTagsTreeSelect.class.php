<?php

class TestFieldDdTagsTreeSelect extends TestFieldDdTagsTreeAbstract {

  function a($tagId2, $v) {
    $item = static::$im->items->getItemF($this->itemId);
    $this->assertTrue($item['sample']['id'] == $this->tagId1, $item['sample']['id'].' != '.$this->tagId1);
    $this->assertTrue($item['sample']['childNodes'][0]['id'] == $tagId2, $item['sample']['childNodes'][0]['id'].' != '.$tagId2);
    $this->fillForm();
    $this->formTest(static::$im->form->html(), $tagId2, $v);
    $this->ddoTest($item, $tagId2, $v);
  }

  function createData() {
    return ['sample' => $this->tagId2];
  }

  function runTests($request = false) {
    $this->a($this->tagId2, $this->v2);
    $this->updateItem(['sample' => $this->tagId3], $request);
    $this->a($this->tagId3, $this->v3);
  }

  function formTest($html, $tagId, $v) {
    $this->assertTrue((bool)strstr($html, 'name="sample" value="'.$tagId.'" id="f_sample_'.$tagId.'" checked />'), 'value="'.$tagId.'" id="f_sample_'.$tagId." checked\n--\n".$html);
  }

  function ddoTest($item, $tagId, $v) {
    $this->assertTrue((bool)strstr((new Ddo('a', 'siteItem'))->setItem($item)->els(), '<span><a href="//t2.sample.'.$this->tagId1.'">'.$this->v1.'</a> → <a href="//t2.sample.'.$tagId.'">'.$v.'</a></span>'));
  }

}