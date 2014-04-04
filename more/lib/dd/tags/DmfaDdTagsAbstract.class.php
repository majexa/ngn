<?php

abstract class DmfaDdTagsAbstract extends Dmfa {

  function elBeforeDelete(FieldEAbstract $el) {
    $this->deleteTagItems($el['name']);
  }

  protected function deleteTagItems($fieldName) {
    DdTags::items($this->dm->strName, $fieldName)->delete($this->dm->id);
  }

}