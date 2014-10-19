<?php

/**
 * Служит только для получения записей без указания конкретного раздела
 */
class DdItems extends Items {

  public $strName;

  public $strData;

  function __construct($strName, Db $db = null) {
    $this->strName = $strName;
    parent::__construct(DdCore::table($this->strName), $db);
  }

  protected $fields;

  /**
   * @return DdFields
   */
  protected function fields() {
    if (isset($this->fields)) return $this->fields;
    return $this->fields = new DdFields($this->strName, ['getHidden' => true]);
  }

  function getItems() {
    if (!($items = $this->getItems_cache())) return [];
    return $items;
  }

  function getItemsF() {
    if (!($items = $this->getItemsF_cache())) return [];
    return $items;
  }

  function getItems2() {
    $this->setTStampCond();
    if (!($items = parent::getItems())) return [];
    $this->extendItemsFilePaths($items);
    $this->extendItemsTags($items);
    $this->extendItemsUsers($items);
    $this->formatItemsText($items);
    $this->extendItemsNumberRange($items);
    foreach (Hook::paths('dd/extendItems') as $path) include $path;
    foreach ($items as &$item) $item = Arr::unserialize($item);
    $this->extendItems($items);
    return $items;
  }

  protected function _prepareItemsConds() {
    $structure = (new DdStructureItems)->getItemByField('name', $this->strName);
    if (empty($structure)) throw new Exception('Structure "'.$this->strName.'" does not exists');
    if (!isset($this->getNonActive) and !empty($structure['settings']['getNonActive'])) $this->getNonActive = $structure['settings']['getNonActive'];
    if (!empty($structure['settings']['enableManualOrder'])) $this->cond->setOrder('oid');
    elseif (!isset($this->cond->orderCond)) $this->cond->setOrder('dateCreate DESC');
    parent::_prepareItemsConds();
  }

  function getItemsSimple() {
    return parent::getItems_nocache();
  }

  protected function extendItems(array &$items) {
  }

  function getItems_cache() {
    $items = [];
    $ids = $this->getItemIds();
    foreach ($ids as $id) $items[$id] = $this->getItem_cache($id);
    return $items;
  }

  function getItems_nocache() {
    $items = [];
    $ids = $this->getItemIds();
    foreach ($ids as $id) $items[$id] = $this->getItem($id);
    return $items;
  }

  function getItemsF_cache() {
    $items = [];
    $ids = $this->getItemIds();
    foreach ($ids as $id) $items[$id] = $this->getItemF_cache($id);
    return $items;
  }

  protected function cc($id) {
    $cache = DdiCache::c(['strName' => $this->strName]);
    $cache->remove('i'.$id);
    $cache->remove('fi'.$id);
  }

  // --------------------- Варианты кэширования -------------------------

  /*

  function getItems_cache2() {
    $cache = FileCache::c();
    if (!($items = $cache->load('ddItems'.$this->strName))) {
      $items = $this->getItems_nocache();
      $cache->save($items, 'ddItems'.$this->strName);
    }
    return $items;
  }
  
  function getItems_cache3() {
    $ids = $this->getItemIds();
    $items = array();
    foreach ($ids as $id) {
      if (($item = Mem::get('ddItem'.$id)) === false) {
        $item = $this->getItemF($id);
        Mem::set('ddItem'.$id, $item);
      }
      $items[$id] = $item;
    }
    return $items;
  }
  */

  // -----------------------------------------------------------------------

  function getFirstItem() {
    foreach ($this->getItems() as $v) return $v;
    return false;
  }

  function getItem($id) {
    $this->setTStampCond();
    if (($item = parent::getItem($id)) == false) return false;
    $this->extendItemTags($item);
    $this->extendItemNumberRange($item);
    //$this->extendItemUsers($item);
    $this->extendItemFilePaths($item);
    return $item;
  }

  function getItemF_cache($id) {
    $cache = DdiCache::c(['strName' => $this->strName]);
    if (!($item = $cache->load('fi'.$id))) {
      $item = $this->getItemF($id);
      $cache->save($item, 'fi'.$id);
    }
    return $item;
  }

  function getItem_cache($id) {
    $cache = DdiCache::c(['strName' => $this->strName]);
    if (!($item = $cache->load('i'.$id))) {
      $item = $this->getItem($id);
      $cache->save($item, 'i'.$id, [], null);
    }
    return $item;
  }

  function getItem_cache_($id) {
    if (!($item = Mem::get('i'.$id))) {
      $item = $this->getItem($id);
      Mem::set('i'.$id, $item);
    }
    return $item;
  }

  protected function extendItem(array &$item) {
  }

  function getItemNonFormat($id) {
    return $this->getItem($id);
  }

  /**
   * Получает отформатированые данные
   *
   * @param   integer   ID записи
   * @return  array     Массив записи
   */
  function getItemF($id) {
    if (!($item = $this->getItem($id))) return false;
    $this->extendItemUsers($item);
    $this->formatItemText($item);
    $modelClass = 'DdItemF'.ucfirst($this->strName);
    if (class_exists($modelClass)) $item = new $modelClass($item);
    return $item;
  }

  function getItemSimple($id) {
    return parent::getItem($id);
  }

  function getItemByField($key, $val) {
    $this->setTStampCond();
    if (!$item = parent::getItemByField($key, $val)) return false;
    $this->extendItemTags($item);
    $this->extendItemFilePaths($item);
    $this->formatItemText($item);
    $this->extendItem($item);
    return $item;
  }

  function copy($id, $newData = null) {
    $newId = parent::copy($id, $newData);
    if (($tagItems = db()->select("SELECT * FROM tagItems WHERE strName=? AND itemId=?d", $this->strName, $id))) {
      foreach ($tagItems as $v) {
        $v['itemId'] = $newId;
        db()->insert('tagItems', $v);
      }
    }
    return $newId;
  }

  // ********************************************
  // -------------- Data Extenders --------------
  // ********************************************

  private function extendItemsTags(&$items) {
    foreach ($items as &$item) {
      $this->extendItemTags($item);
    }
    return;

    $itemIds = array_keys($items);
    if (!($fields = $this->fields()->getTagFields())) return;
    foreach (db()->query("
    SELECT
      tagItems.itemId,
      tagItems.groupName,
      tagItems.collection,
      tags.id,
      tags.title,
      tags.name,
      tags.cnt
    FROM tagItems
    LEFT JOIN tags ON tagItems.tagId=tags.id
    WHERE
      tagItems.strName=? AND
      tagItems.groupName IN (?a) AND
      tagItems.itemId IN (?a)", $this->strName, array_keys($fields), $itemIds) as $v) {
      $tags[$v['itemId']][$v['groupName']][] = $v;
    }
    foreach ($fields as $fieldName => $field) {
      foreach ($itemIds as $itemId) {
        // $items[$itemId][$fieldName] = null; continue; // debug
        if (FieldCore::hasAncestor($field['type'], 'ddTagsSelect')) {
          $items[$itemId][$fieldName] = isset($tags[$itemId][$fieldName]) ? $tags[$itemId][$fieldName][0] : [];
        }
        elseif (FieldCore::hasAncestor($field['type'], 'ddTagsTreeMultiselect')) {
          $items[$itemId][$fieldName] = DdTags::items($this->strName, $fieldName)->getItems($itemId);
          continue;
          // Формируем массив с разбитием на коллекции тэговых записей
          if (isset($tags[$itemId][$fieldName])) {
            $items[$itemId][$fieldName] = [];
            foreach ($tags[$itemId][$fieldName] as $tag) {
              $items[$itemId][$fieldName][$tag['collection']][] = $tag;
            }
          }
          else {
            $items[$itemId][$fieldName] = [];
          }
        }
        else {
          $items[$itemId][$fieldName] = DdTags::items($this->strName, $fieldName)->getItems($itemId);
        }
      }
    }
  }

  protected function replaceItemByOnDemandObject(&$item, $fieldName) {
    if (is_object($item)) {
      if ($item instanceof DdItemOnDemand) {
        $item->onDemandFields[] = $fieldName;
        return;
      } else {
        throw new Exception('Wrong object type');
      }
    }
    $item = new DdItemOnDemand($this->strName, $item);
    $item->onDemandFields[] = $fieldName;
  }

  /**
   * Добавляет данные для тэгов в массив записи
   *
   * @param $item
   */
  protected function extendItemTags(&$item) {
    $this->setFieldTagTypes();
    foreach (array_keys($item) as $fieldName) {
      if (!isset($this->fieldTagTypes[$fieldName])) continue;
      $fieldType = $this->fieldTagTypes[$fieldName];
      if (DdTags::isTree($fieldType) and DdTags::isMulti($fieldType)) {
        $item[$fieldName] = [];
        $r = DdTags::items($this->strName, $fieldName);
        if ($r->getTreeCount($item['id']) > 50) {
          $this->replaceItemByOnDemandObject($item, $fieldName);
        } else {
          foreach (DdTags::items($this->strName, $fieldName)->getTree($item['id']) as $node) {
            $item[$fieldName][$node['collection']] = TreeCommon::flat([$node]);
          }
        }
      }
      else {
        $tagItems = new DdTagsItems($this->strName, $fieldName);
        $tagItems->getRelatedItems = true;
        $r = $tagItems->getItems($item['id']);
        if (DdTags::isMulti($fieldType)) {
          $item[$fieldName] = $r;
        }
        else {
          $item[$fieldName] = $r ? $r[0] : null;
        }
      }
    }
  }

  protected function extendItemTagIds(&$item) {
    $this->extendItemTags($item);
    return;
    $this->setFieldTagTypes();
    foreach (array_keys($item) as $fieldName) {
      if (!isset($this->fieldTagTypes[$fieldName])) continue;
      $fieldType = $this->fieldTagTypes[$fieldName];
      if (DdTags::isTree($fieldType)) {
        $r = DdTags::items($this->strName, $fieldName)->getLastTreeNodes($item['id']);
      }
      else {
        $r = DdTags::items($this->strName, $fieldName)->getItems($item['id']);
      }
      $item[$fieldName] = $r ? (DdTags::isMulti($fieldType) ? $r : $r[0]) : null;
    }
  }

  protected function extendItemsNumberRange(&$items) {
    foreach ($this->fields()->getFieldsByType('numberRange') as $field) {
      foreach ($items as &$item) {
        $this->extendNumberRange($field, $item);
      }
    }
  }

  protected function extendItemNumberRange(&$item) {
    foreach ($this->fields()->getFieldsByType('numberRange') as $field) {
      $this->extendNumberRange($field, $item);
    }
  }

  protected function extendNumberRange($field, &$item) {
    if (!isset($item[$field['name'].'From'])) throw new Exception('Item "'.$field['name'].'From" field is empty');
    $item[$field['name']] = [
      'from' => $item[$field['name'].'From'],
      'to'   => $item[$field['name'].'To']
    ];
    unset($item[$field['name'].'From']);
    unset($item[$field['name'].'To']);
  }

  /*
  protected function extendItemDd(&$item) {
    foreach (array_filter($this->fields()->getFields(), function($v) {DdTags::isDdItemsType($v['type']})) as $f) {
      $this->extendDd($f, $item);
    }
  }

  protected function extendItemsDd(&$items) {
    foreach ($this->fields()->getFieldsByAncestor('ddItemSelect') as $f) {
      foreach ($items as &$item) {
        $this->extendDd($f, $item);
      }
    }
  }

  protected function extendDd($field, &$item) {
    if (empty($item[$field['name']])) return;
    $item[$field['name']] = O::get('DdItems', $field['settings']['strName'])->getItemSimple($item[$field['name']]);
  }
  */

  public $fieldTagTypes;

  private function setFieldTagTypes() {
    if (isset($this->fieldTagTypes)) return;
    $this->fieldTagTypes = Arr::get($this->fields()->getTagFields(), 'type', 'name');
  }

  private function extendItemsUsers(&$items) {
    foreach ($this->fields()->getFields() as $name => $v) {
      if ($v['type'] == 'user') $names[] = $name;
    }
    if (!isset($names)) return;
    foreach ($items as &$item) foreach ($names as $name) $item[$name] = DbModelCore::get('users', $item[$name]);
  }

  private function extendItemUsers(&$item) {
    // необходимо получить поля типа юзер. те что присутствуют item'e
    foreach ($this->fields()->getFields() as $name => $v) {
      if (!isset($item[$name])) continue;
      if ($v['type'] == 'user') {
        if ($item[$name] and !is_numeric($item[$name])) throw new Exception("FUCK $this->strName {$item['id']}");
        $item[$name] = DbModelCore::get('users', $item[$name]);
      }
    }
  }

// ------ Exif Extender ------

  private function extendItemExif(&$item) {
    return;
    foreach ($this->fields()->getFieldsByAncestor('image') as $fieldName => $v) {
      $item[$fieldName.'_exif'] = exif_read_data(Misc::getWebFileAbsPath($v));
    }
  }

// ------ File Paths Extender ------

  private function extendItemsFilePaths(&$items) {
    foreach ($items as &$v) {
      $this->extendItemFilePaths($v);
    }
  }

  private function extendItemFilePaths(&$item) {
    $types = $this->fields()->getTypes();
    foreach (array_keys($this->fields()->getFileFields()) as $name) {
      if (empty($item[$name]) or !file_exists(UPLOAD_PATH.'/'.$item[$name])) {
        $item[$name] = '';
        $item['sm_'.$name] = '';
        $item['md_'.$name] = '';
        continue;
      }
      else {
        $item[$name.'_fSize'] = filesize(UPLOAD_PATH.'/'.$item[$name]);
        if (FieldCore::hasAncestor($types[$name], 'image')) {
          $item[$name] = '/'.UPLOAD_DIR.'/'.$item[$name];
          $item['sm_'.$name] = Misc::getFilePrefexedPath($item[$name], 'sm_');
          $item['md_'.$name] = Misc::getFilePrefexedPath($item[$name], 'md_');
        }
        else {
          $item[$name] = '/'.UPLOAD_DIR.'/'.$item[$name];
        }
      }
    }
  }

// ------ Common Extender ------

  /*
    private function extendItemsCommon(&$items) {
      return;
      $n = 0;
      foreach ($items as &$item) {
        $item['link'] = Tt()->getPath(0).'/'.$item['pagePath'].'/'.$item['id'];
        $item['n'] = $n;
        $n++;
      }
    }
  */

// ------ Text Formatter ------

  protected function formatItemsText(&$items) {
    foreach ($items as &$item) {
      $this->formatItemText($item);
    }
  }

  protected function formatItemText(&$item) {
    return;
    foreach ($this->fields()->getFields() as $name => $v) {
      if ($v['type'] == 'textarea') {
        $item[$name] = $item[$name.'_f'];
      }
    }
  }

// ------ Timestamp Condition ------

  private function setTStampCond() {
    foreach (array_keys($this->fields()->getDateFields()) as $fieldName) {
      $this->addSelectCond("UNIX_TIMESTAMP({$this->table}.$fieldName) AS {$fieldName}_tStamp");
    }
  }

  protected function getFilterItemIds($tagField, $tagValues, $byId = null) {
    $tagValues = (array)$tagValues;
    $tags = DdTags::get($this->strName, $tagField);
    if (!$byId and $tags->group->tree) throw new Exception("Getting tags by name supportes only flat tags. '$tagField' is tree type tag.");
    $itemIds = [];
    if (!$byId) {
      foreach ($tagValues as &$v) {
        $v = db()->selectCell("SELECT id FROM {$tags->group->table} WHERE strName=? AND name=?", $this->strName, $v);
      }
    }
    foreach ($tagValues as $v) {
      $tag = DbModelCore::get($tags->group->table, $v);
      if ($tag === false) throw new Exception('There is no such tag: '.$v);
      $itemIds = Arr::append($itemIds, DdTags::items($this->strName, $tagField)->getIdsByTagId($tag['id']));
    }
    if (empty($itemIds)) $itemIds = -1; // Если нет тэгов, делаем значение фильтра таким, что бы выборка была нулевая
    return $itemIds;
  }

  /**
   * @param string
   * @param array|integer
   * @param bool $byId
   * @return $this
   * @throws Exception
   */
  function addTagFilter($tagField, $tagValues, $byId = true) {
    return $this->addF('id', $this->getFilterItemIds($tagField, $tagValues, $byId));
  }

  /**
   * @param string
   * @param array|integer
   * @param bool $byId
   * @return $this
   * @throws Exception
   */
  function addNotTagFilter($tagField, $tagValues, $byId = true) {
    $this->cond->addNotInFilter('id', $this->getFilterItemIds($tagField, $tagValues, $byId));
    return $this;
  }

  function reorderItems($ids) {
    DbShift::items($ids, $this->table);
  }

  function activate($id) {
    db()->query("UPDATE tagItems SET active=1 WHERE strName=? AND itemId=?", $this->strName, $id);
    parent::activate($id);
  }

  function deactivate($id) {
    db()->query("UPDATE tagItems SET active=0 WHERE strName=? AND itemId=?", $this->strName, $id);
    parent::deactivate($id);
  }

}
