<?php

class FieldEDdTagsTreeSelect extends FieldEText {

  static $ddTags = true, $ddTagsTree = true;

  protected function getTags() {
    return new DdTagsTagsTree(new DdTagsGroup($this->form->strName, $this->options['name']));
  }
  
  function _html() {
    $tags = $this->getTags();
    if (!empty($this->form->ctrl->userGroup)) {
      $tags->getCond($this->form->strName)->addF('userGroupId', $this->form->ctrl->userGroup['id']);
    }
    return $this->options['value'].Tt()->getTpl('dd/tagsTreeSelect', [
      'name' => $this->options['name'],
      'value' => $this->options['value'],
      'required' => $this->options['required'],
      'tree' => $tags->getTree()
    ]);
  }
  
  function _js() {
    return $this->typeJs();
  }
  
}
