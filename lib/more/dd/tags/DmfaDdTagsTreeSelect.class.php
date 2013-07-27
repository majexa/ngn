<?php

class DmfaDdTagsTreeSelect extends DmfaDdTagsAbstract {

  static $firstElementInSet = true;

  protected function getTags($name) {
    return new DdTagsTagsTree(new DdTagsGroup($this->dm->strName, $name));
  }

  function afterCreateUpdate($v, $k) {
    // Если данные этого поля пустые
    if (empty($v)) {
      // Удаляем текущие тэг-записи
      $this->beforeDelete($k);
      return;
    }
    $name = BracketName::getPureName($k);
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
  }

}