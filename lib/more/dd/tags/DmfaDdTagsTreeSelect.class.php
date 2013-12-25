<?php

class DmfaDdTagsTreeSelect extends DmfaDdTagsAbstract {

  static $firstElementInSet = true;

  function source2formFormat($v) {
    return $v['id'];
  }

  protected function getTags($name) {
    return new DdTagsTagsTree(new DdTagsGroup($this->dm->strName, $name));
  }

  function afterCreateUpdate($tagId, $k) {
    if (empty($tagId)) {
      $this->deleteTagItems($k);
      return;
    }
    DdTags::items($this->dm->strName, $k)->createByIds(
      $this->dm->id,
      $this->getTags($k)->getParentIds2($tagId),
      true
    );
    /*
    die2();
    //die2();
    //$name = BracketName::getPureName($k);
    //die2([$k, $name]);
    $delete = true;
    if (BracketName::getPureName($k)) {
      // Если это экшн для набора элементов, удаляем предыдущие тэг-записи только для первого элемента набора
      if (!in_array($name, Dmfa::$processedNames)) {
        $delete = true;
        Dmfa::$processedNames[] = $name;
      } else {
        $delete = false;
      }
    }
    DdTags::items($this->dm->strName, $k)->createByIds(
      $this->dm->id,
      $this->getTags($name)->getParentIds2(Arr::last((array)$v)),
      $delete
    );
    */
  }

}