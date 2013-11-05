<?php

DdFieldCore::registerType('ddTagsTreeSelect', [
  'dbType' => 'VARCHAR',
  'dbLength' => 255,
  'title' => 'Древовидный выбор одного тэга',
  'order' => 240,
  'tags' => true,
  'tagsTree' => true
]);

class FieldEDdTagsTreeSelect extends FieldEText {

  protected function getTags() {
    return new DdTagsTagsTree(new DdTagsGroup($this->form->strName, $this->options['name']));
  }
  
  function _html() {
    $tags = $this->getTags();
    if (!empty($this->form->ctrl->userGroup)) {
      $tags->getCond($this->form->strName)->addF('userGroupId', $this->form->ctrl->userGroup['id']);
    }
    return Tt()->getTpl('dd/tagsTreeSelect', [
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
