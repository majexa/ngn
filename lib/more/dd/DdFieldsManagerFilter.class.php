<?php

class DdFieldsManagerFilter extends DdFieldsManager {

  /**
   * @var DbModel
   */
  protected $masterStr;

  protected function replaceData() {
    parent::replaceData();
    $this->data['defaultDisallow'] = true;
    //$this->data['system'] = true;
    $this->data['filterable'] = false;
    // Фильтруемая структура та, у которой указана в виде фильтра структура текущего менеджера полей
    $this->filterableStr = DbModelCore::get('dd_structures', $this->strName, 'filterStrName');
    if (DdTags::isTag($this->data['type'])) {
      $this->data['type'] = $this->getFilterType($this->data['type']);
      $this->data['required'] = false;
    } elseif (DdFieldCore::isNumberType($this->data['type'])) {
      $this->data['type'] = 'numberRange';
      $this->data['required'] = false;
    }
  }

  protected function getFilterType($filterableType) {
    if (FieldCore::hasAncestor($filterableType, 'ddCity')) return 'ddCityMultiselect';
    //elseif (FieldCore::hasAncestor($filterableType, 'ddMetro')) return 'ddMetroMultiselect';
    else return DdTags::isTree($filterableType) ? 'ddTagsTreeMultiselect' : 'ddTagsMultiselect';
  }

  protected function afterCreateUpdate() {
    db()->query("UPDATE tagGroups SET masterStrName='{$this->filterableStr['name']}' WHERE strName=? AND name=?", $this->strName, $this->data['name']);
  }

}