<?php

class TestFieldDdTagsConsecutiveSelect extends TestFieldDdTagsTreeSelect {

  function formTest($html, $tagId, $v) {
    $this->assertTrue((bool)strstr($html, '<option value="'.$this->tagId1.'" selected>'.$this->v1.'</option>'));
    $this->assertTrue((bool)strstr($html, '<option value="'.$tagId.'" selected>'.$v.'</option>'));
  }

  function ddoTest($item, $tagId, $v) {
    $html = (new Ddo('a', 'siteItem'))->setItem($item)->els();
    $this->assertTrue((bool)strstr($html, ">$this->v1</a> →"));
    $this->assertTrue((bool)strstr($html, ">$v</a>"));
  }

}