<?php

class DdTags {

  static function isTag($fieldType) {
    return FieldCore::staticProperty($fieldType, 'ddTags');
  }

  static function isTree($fieldType) {
    return FieldCore::staticProperty($fieldType, 'ddTagsTree');
  }

  static function isDdItems($fieldType) {
    return !empty(DdFieldCore::getTypeData($fieldType, false)['ddItems']);
  }

  static function isMulti($fieldType) {
    return FieldCore::staticProperty($fieldType, 'ddTagsMulti');
  }

  static function isItemsDirected($type) {
    return FieldCore::staticProperty($type, 'ddTagsItemsDirected');
  }

  static function getLink($path, array $tag, $id = false) {
    return $path.'/t2.'.$tag['groupName'].'.'.$tag['id'];
  }

  static function title2name($title) {
    return trim(Misc::transit($title, true), '-');
  }

  static function rebuildCounts() {
    db()->select('UPDATE tags SET cnt=0');
    foreach ((db()->select('
    SELECT strName, groupName, tagId AS id, COUNT(*) AS cnt
    FROM tagItems GROUP BY strName, groupName, tagId')) as $v) {
      db()->select('UPDATE tags SET cnt=?d WHERE strName=? AND groupName=? AND id=?d', $v['cnt'], $v['strName'], $v['groupName'], $v['id']);
    }
  }

  static function rebuildNames() {
    foreach (db()->query('SELECT id, title FROM tags') as $v) {
      db()->query('UPDATE tags SET name=? WHERE id=?d', DdTags::title2name($v['title']), $v['id']);
    }
  }

  /**
   * Обнуляет несуществующие parentId
   */
  static function rebuildParents() {
    foreach (db()->select('
    SELECT
      tags.strName,
      tags.groupName,
      tags.id,
      tags.parentId
    FROM tags
    LEFT JOIN tagGroups ON
      tagGroups.strName=tags.strName AND
      tagGroups.name=tags.groupName
    WHERE tagGroups.tree=1') as $v) {
      $ids[$v['strName']][$v['groupName']][] = $v['id'];
      if ($v['parentId']) $parentIds[$v['strName']][$v['groupName']][] = $v['parentId'];
    }
    foreach ($parentIds as $strName => $v1) {
      foreach ($v1 as $groupName => $v2) {
        foreach ($v2 as $parentId) {
          if (!in_array($parentId, $ids[$strName][$groupName])) db()->query('
            UPDATE tags SET parentId=0
            WHERE parentId=?d AND strName=? AND groupName=?', $parentId, $strName, $groupName);
        }
      }
    }
  }

  // ------------- instance getters -----------

  /**
   * @return DdTagsItems
   */
  static function items($strName, $groupName) {
    return O::get('DdTagsItems', $strName, $groupName);
  }

  static function tag($group, $id, $param = null) {
    return DbModelCore::get($group->table, $id, $param);
  }

  /**
   * @param   string   Structure string name
   * @param   string   Tags group name
   * @return  DdTagsTagsBase
   */
  static function get($strName, $groupName) {
    $group = O::get('DdTagsGroup', $strName, $groupName);
    return $group->tree ? new DdTagsTagsTree($group) : new DdTagsTagsFlat($group);
  }

  /**
   * @param   integer   Tags group ID
   * @return  DdTagsTagsBase
   */
  static function getByGroupId($groupId) {
    $group = DdTagsGroup::getById($groupId);
    return $group->tree ? new DdTagsTagsTree($group) : new DdTagsTagsFlat($group);
  }

  static function cleanupEmptyItems() {
    foreach (DdCore::tables() as $table) {
      $strName = Misc::removePrefix('dd_i_', $table);
      $r = db()->query("
SELECT tagItems.tagId, tagItems.itemId, $table.id
FROM tagItems
LEFT JOIN $table ON $table.id=tagItems.itemId
WHERE tagItems.strName='$strName' AND $table.id IS NULL");
      foreach ($r as $v) db()->query("DELETE FROM tagItems WHERE tagId={$v['tagId']} AND itemId={$v['itemId']}");
    }
  }

}