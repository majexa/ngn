<?php

class TestFieldDdTagsConsecutiveSelect extends TestFieldDdTagsTreeSelect {

  function formTest($html, $tagId2, $v) {
    $this->assertTrue((bool)strstr($html, '<option value="'.$this->tagId1.'" selected>'.$this->v1.'</option>'));
    $this->assertTrue((bool)strstr($html, '<option value="'.$tagId2.'" selected>'.$v.'</option>'));
  }

  function ddoTest($item, $tagId2, $v) {
  }

}