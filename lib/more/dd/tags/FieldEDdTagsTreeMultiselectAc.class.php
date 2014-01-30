<?php

class FieldEDdTagsTreeMultiselectAc extends FieldEDdTags {

  static $ddTagsTree = true, $ddTagsMulti = true;

  protected function prepareInputValue($v) {
    return '';
  }

  function jsInline() {
    if (empty($this->options['value'])) return '';
    $v = Arr::filterByKeys2($this->options['value'], ['id', 'title']);
    return "Ngn.toObj('Ngn.Form.El.DdTags.values.{$this->form->id()}.{$this->options['name']}', ".Arr::jsArr($v).");";
  }

}