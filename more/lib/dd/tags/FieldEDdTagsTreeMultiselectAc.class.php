<?php

class FieldEDdTagsTreeMultiselectAc extends FieldEDdTags {

  static $ddTagsTree = true, $ddTagsMulti = true;

  protected function formatValue() {
    $tags = DdTags::get($this->form->strName, $this->baseName);
    $ids = explode(',', $this->options['value']);
    $tags->getSelectCond()->addF('id', $ids);
    return $tags->getData();
  }

  protected function prepareInputValue($value) {
    return '';
  }

  function jsInline() {
    if (empty($this->options['value'])) return '';
    $v = Arr::filterByKeys2($this->options['value'], ['id', 'title']);
    return "Ngn.Object.fromString('Ngn.Form.El.DdTagsAc.values.{$this->form->id()}.{$this->options['name']}', ".Arr::jsArr($v).");";
  }

}