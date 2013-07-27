<?php

DdFieldCore::registerType('ddTagsTreeMultiselect', [
  'dbType'   => 'VARCHAR',
  'dbLength' => 255,
  'title'    => 'Древовидный выбор нескольких тэгов',
  'order'    => 250,
  'tags'     => true,
  'tagsTree' => true
]);

class FieldEDdTagsTreeMultiselect extends FieldEText {
use DdElement;

  protected $useDefaultJs = true;

  function _html() {
    $data = self::getTplData(new DdTagsTagsTree(new DdTagsGroup($this->strName, $this->options['name'])), $this->options['name'], $this->options['value']);
    return Tt()->getTpl('dd/tagsTreeMultiselect', array_merge($data, ['dataParams' => isset($this->options['dataParams']) ? $this->options['dataParams'] : []]));
  }

  static function getTplData(DdTagsTagsTree $tags, $fieldName, $value, $parentId = null, $inner = false) {
    $tree = $tags->getTree($parentId);
    if ($tags->getNodesTotalCount() > 200) {
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