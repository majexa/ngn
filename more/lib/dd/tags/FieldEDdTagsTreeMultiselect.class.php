<?php

class FieldEDdTagsTreeMultiselect extends FieldEText {
  use DdElement;

  static $ddTags = true, $ddTagsTree = true, $ddTagsMulti = true;

  protected function defineOptions() {
    return array_merge(parent::defineOptions(), [
      'rootTagId' => 0
    ]);
  }

  function _html() {
    $data = self::getTplData(new DdTagsTagsTree(new DdTagsGroup($this->strName, $this->baseName)), $this->options['name'], $this->options['value'], $this->options['rootTagId'], !empty($this->options['value']));
    if ($this->options['required']) $this->options['dataParams']['required'] = true;
    return Tt()->getTpl('dd/tagsTreeMultiselect', array_merge($data, [
      'dataParams' => isset($this->options['dataParams']) ? $this->options['dataParams'] : []
    ]));
  }

  static function getTplData(DdTagsTagsTree $tags, $fieldName, $value, $parentId = null, $forceNodesLimit = false) {
    $tree = $tags->_getTree($parentId);
    if (!$forceNodesLimit and $tags->getNodesTotalCount() > 200) {
      foreach ($tree as &$v) {
        if (!empty($v['childNodes'])) {
          $v['childNodes'] = [];
          $v['childNodesExists'] = true;
        }
      }
    }
    return [
      'name'  => $fieldName,
      'tree'  => $tree,
      'value' => $value,
    ];
  }

}