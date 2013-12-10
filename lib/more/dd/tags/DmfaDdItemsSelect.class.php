<?php

class DmfaDdItemsSelect extends Dmfa {

  function elAfterCreateUpdate(FieldEAbstract $el) {
    DdTags::items($this->dm->strName, $el['name'])->createByIds($this->dm->id, (array)$el->value());
  }

}