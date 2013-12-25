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
    return $this->fields = O::get('DdFields', $this->strName, ['getHidden' => true]);
  }

  function getItems() {
    $this->setTStampCond();
    if (!($items = parent::getItems())) return [];
    $this->extendItemsFilePaths($items);
    $this->extendItemsTags($items);
    $this->extendItemsUsers($items);
    $this->formatItemsText($items);
    $this->extendItemsNumberRange($items);
    if (($paths = Hook::paths('dd/extendItems')) !== false) foreach ($paths as $path) include $path;
    foreach ($items as &$item) $item = Arr::unserialize($item);
    $this->extendItems($items);
    return $items;
  }

  protected function _prepareItemsConds() {
    $structure = (new DdStructureItems)->getItemByField('name', $this->strName);
    if (empty($structure)) throw new Exception('Structure "'.$this->strName.'" does not exists');
    if (!empty($structure['settings']['getNonActive'])) $this->getNonActive = $structure['settings']['getNonActive'];
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
    $ids = $this->getItemIds();
    $cache = NgnCache::c();
    $items = [];
    foreach ($ids as $id) {
      if (!($item = $cache->load('ddItem'.$this->strName.$id))) {
        $item = $this->getItemF($id);
        $cache->save($item, 'ddItem'.$this->strName.$id);
      }
      $items[$id] = $item;
    }
    return $items;
  }

  protected function cc($id) {
    NgnCache::c()->remove('ddItem'.$this->strName.$id);
  }

  // --------------------- Варианты кэширования -------------------------

  /*

  function getItems_cache2() {
    $cache = NgnCache::c();
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
    $this->extendItemTagIds($item);
    $this->extendItemNumberRange($item);
    return $item;
  }

  function getItemF_cache($id) {
    if (!($item = NgnCache::c()->load('ddItem'.$this->strName.$id))) {
      $item = $this->getItemF($id);
      NgnCache::c()->save($item, 'ddItem'.$this->strName.$id);
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
    if (!($item = parent::getItem($id))) return false;
    $this->extendItemFilePaths($item);
    $this->extendItemTags($item);
    $this->extendItemUsers($item);
    //$this->extendItemExif($item);
    $this->formatItemText($item);
    $this->extendItemNumberRange($item);
    //$this->extendItemDd($item);
    $this->extendItem($item);
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
  // -------------- Data Extenders ---------------
  // ********************************************

  // ------ Tags Extender ------

  private function extendItemsTags(&$items) {
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

  /**
   * Добавляет данные для тэгов в массив записи
   *
   * @param   array Массив записи
   */
  private function extendItemTags(&$item) {
    $this->setFieldTagTypes();
    foreach (array_keys($item) as $fieldName) {
      if (!isset($this->fieldTagTypes[$fieldName])) continue;
      $fieldType = $this->fieldTagTypes[$fieldName];
      if (DdTags::isTree($fieldType) and DdTags::isMulti($fieldType)) {
        $item[$fieldName] = [];
        foreach (DdTags::items($this->strName, $fieldName)->getFlat($item['id']) as $tag) {
          $item[$fieldName][$tag['collection']][] = $tag;
        }
      }
      else {
        $tagItems = DdTags::items($this->strName, $fieldName);
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

  protected function extendItemTagIds(&$item) {
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
      $item[$fieldName] = DdTags::isMulti($fieldType) ? $r : $r[0];
      continue;


      if (FieldCore::hasAncestor($fieldType, 'ddTagsTreeMultiselectAc')) {
        $item[$fieldName] = DdTags::items($this->strName, $fieldName)->getLastTreeNodes($item['id']);
      }
      elseif (FieldCore::hasAncestor($fieldType, 'ddTags')) {
        $tags = db()->selectCol(<<<SQL
SELECT tags.title FROM tagItems, tags
WHERE
  tagItems.groupName =? AND
  tagItems.strName =? AND
  tagItems.itemId =?d AND
  tagItems.tagId = tags.id
SQL
          , $fieldName, $this->strName, $item['id']);
        $item[$fieldName] = implode(', ', $tags);
      }
      elseif (DdTags::isTree($fieldType)) {
        if (FieldCore::hasAncestor($fieldType, 'ddTagsTreeSelect')) {
          $item[$fieldName] = DdTags::items($this->strName, $fieldName)->getLastTreeNodes($item['id'])[0]['tagId'];
        }
        else {
          //$item[$fieldName] = Arr::get(DdTags::items($this->strName, $fieldName)->getLastTreeNodes($item['id']), 'tagId');
          $item[$fieldName] = TreeCommon::getFlatParams(DdTags::items($this->strName, $fieldName)->getTree($item['id']), 'id');
        }
      }
      else {
        $r = Arr::get(DdTags::items($this->strName, $fieldName)->getItems($item['id']), 'id');
        if (DdTags::isMulti($fieldType)) {
          $item[$fieldName] = $r;
        }
        elseif ($r) {
          $item[$fieldName] = $r[0];
        }
      }
    }
  }

  protected $fieldTagTypes;

  private function setFieldTagTypes() {
    if (isset($this->fieldTagTypes)) return;
    $this->fieldTagTypes = Arr::get($this->fields()->getTagFields(), 'type', 'name');
  }

  private function extendItemsUsers(&$items) {
    foreach ($this->fields()->getFields() as $name => $v) {
      if ($v['type'] == 'user') $names[] = $name;
    }
    if (!isset($names)) return;
    //foreach ($items as &$item) foreach ($names as $name) $item[$name] = DbModelCore::get('users', $item[$name]);
  }

  private function extendItemUsers(&$item) {
    // необходимо получить поля типа юзер. те что присутствуют item'e
    foreach ($this->fields()->getFields() as $name => $v) {
      if (!isset($item[$name])) continue;
      if ($v['type'] == 'user') {
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
        $item[$name] = ''; // $item[$name].' not exists';
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

}