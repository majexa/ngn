<?php

abstract class DmfaDdTagsAbstract extends DmfaDd {

  function elBeforeDelete(FieldEAbstract $el) {
    $this->deleteTagItems($el['name']);
  }

  protected function deleteTagItems($fieldName) {
    DdTags::items($this->dm->strName, $fieldName)->delete($this->dm->id);
  }

}