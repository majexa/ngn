<?php

class DdTagsItems {

  public $strName, $groupName, $group;

  static $disableUpdateCount = false;
  static $getNonActive = false;

  function __construct($strName, $groupName) {
    $this->strName = $strName;
    $this->group = new DdTagsGroup($this->strName, $groupName);
  }

  /**
   * Создает запись тэга
   *
   * @param   string  Имя группы
   * @param   integer ID раздела
   * @param   integer ID записи
   * @param   array   Названия тега
   * @return  mixed   ID в случае успеха или false в случае неудачи
   */
  function create($itemId, array $titles, $strict = false) {
    if ($this->group->tree) throw new Exception("Tag Items of 'Tree' type can not be created by titles");
    // Удаляем текущие ТэгЗаписи
    $titles = Arr::filterEmpties($titles, false);
    $curItems = $this->getItems($itemId);
    $this->_delete($itemId);
    $tags = new DdTagsTagsFlat($this->group);
    if ($this->group->itemsDirected) {
      // ТэгЗаписи влияют на Тэги
      // Удаляем те Тэги, заголовков которых нет в будующих ТэгЗаписях
      foreach ($curItems as $v) {
        if (!in_array($v['title'], $titles)) {
          $tags->delete($v['tagId']);
        }
      }
    }
    foreach ($titles as $title) {
      $tag = $tags->getByTitle($title);
      if (!$tag) {
        if ($strict) throw new NotFoundException("Tag with title '$title' not found (strName=$strName, groupName=$groupName)");
        if (!$this->group->itemsDirected) // Если ТэгЗаписи не влияют на Тэги
        continue;
        $tagId = $tags->create(['title' => $title]);
      }
      else {
        $tagId = $tag['id'];
      }
      $this->_create($tagId, $itemId); // Создаем ТэгЗапись
      $this->updateCount($tagId);
    }
  }

  // работает как replace
  function createByIds($itemId, array $tagIds, $delete = true) {
    if ($delete) $this->delete($itemId);
    foreach ($tagIds as $tagId) {
      if (!DbModelCore::get($this->group->table, $tagId)) continue;
      $this->_create($tagId, $itemId); // Создаем ТэгЗапись
      $this->updateCount($tagId);
    }
  }

  function createById($itemId, $tagId) {
    $this->delete($itemId);
    $this->_create($tagId, $itemId);
    $this->updateCount($tagId);
  }

  function createByIdsCollection($itemId, array $collectionTagIds, $replace = true) {
    if ($replace) $this->delete($itemId);
    $allTagTds = [];
    if (!$collectionTagIds) return;
    foreach ($collectionTagIds as $collection => $tagTds) {
      foreach ($tagTds as $tagId) {
        $allTagTds[] = $tagId;
        $data[] = [
          'groupName'  => $this->group->name,
          'strName'    => $this->strName,
          'tagId'      => $tagId,
          'itemId'     => $itemId,
          'collection' => $collection
        ];
      }
    }
    db()->insertLarge('tags_items', $data);
    $this->updateCounts(array_unique($allTagTds));
  }

  function updateCount($tagId) {
    if (self::$disableUpdateCount) return false;
    Misc::checkEmpty($tagId);
    $cnt = db()->selectCell("
    SELECT COUNT(*) FROM
    (
      SELECT * FROM tags_items
      WHERE strName=? AND tagId=?d AND active=1
      GROUP BY itemId
    ) AS t
    ", $this->strName, $tagId);
    db()->query('UPDATE tags SET cnt=?d WHERE id=?d', $cnt, $tagId);
    return $cnt;
  }

  function updateCounts(array $tagIds) {
    if (self::$disableUpdateCount) return;
    if (!$tagIds) return;
    $r = db()->select(<<<SQL
SELECT tagId, COUNT(*) AS cnt FROM tags_items
WHERE strName=? AND tagId IN (?a) AND active=1
GROUP BY tagId
SQL
      , $this->strName, $tagIds);
    foreach ($r as $v) {
      //output("update count");
      db()->query('UPDATE tags SET cnt=?d WHERE id=?d', $v['cnt'], $v['tagId']);
    }
  }

  public function _create($tagId, $itemId, $collection = 0) {
    db()->query('INSERT INTO tags_items SET groupName=?, strName=?, tagId=?d, itemId=?d, collection=?d', $this->group->name, $this->strName, $tagId, $itemId, $collection);
  }

  function _delete($itemId) {
    db()->query('DELETE FROM tags_items WHERE strName=? AND groupName=? AND itemId=?d', $this->strName, $this->group->name, $itemId);
  }

  /**
   * Удаляет все тэг-записи определенной dd-записи в группе,
   * обновляет кол-во записей в тегах
   *
   * @param  string  Имя структуры
   * @param  string  Имя группы
   * @param  integer ID dd-записи
   */
  function delete($itemId) {
    $tagItems = $this->getFlat($itemId);
    $this->_delete($itemId);
    foreach ($tagItems as $v) {
      if (empty($v['id'])) throw new Exception("Empty tag on deleting. strName={$this->strName}, itemId=$itemId, tag: ".getPrr($v));
      $this->updateCount(Misc::checkEmpty($v['id']));
    }
  }

  /**
   * Удаляет все тег-записи определенного тэга,
   * обновляет кол-во записей в этом тэге
   *
   * @param  string  Имя структуры
   * @param  string  Имя группы
   * @param  integer ID тэга
   */
  function deleteByTagId($tagId) {
    db()->query('DELETE FROM tags_items WHERE strName=? AND groupName=? AND tagId=?d', $this->strName, $this->group->name, $tagId);
    $this->updateCount($tagId);
  }

  function getLastTreeNodes($itemId) {
    if (!($nodes = $this->getTree([$itemId]))) return;
    $r = [];
    foreach ($nodes as $node) {
      while (1) {
        if (empty($node['childNodes'])) {
          $r[] = $node;
          break;
        }
        $node = $node['childNodes'][0];
      }
    }
    return $r;
  }

  function getIdsByTagId($tagId) {
    $activeCond = self::$getNonActive ? '' : 'AND active=1';
    return db()->selectCol("
    SELECT itemId FROM tags_items
    WHERE strName=? AND groupName=? AND tagId=?d $activeCond", $this->strName, $this->group->name, $tagId);
  }

  /**
   * Возвращает тэг-записи, выстроенные в дерево
   *
   * @param   string          Имя структуры
   * @param   integer/array   ID dd-записей
   */
  function getTree($itemIds) {
    $itemIds = (array)$itemIds;
    $items = $this->getFlat($itemIds);
    foreach (array_keys($items) as $k) {
      if ($items[$k]['parentId']) {
        unset($items[$k - 1]);
        $items[$k] = $this->injectInParent($items[$k]['parentId'], $items[$k]);
      }
    }
    return array_values($items);
  }

  function getFlat($itemIds) {
    $itemIds = (array)$itemIds;
    $params = [
      'tags_items.*',
      'tags_items.tagId AS id', // нужно для построения дерева
      "tags.title",
    ];
    if ($this->group->allowEdit) {
      $params[] = 'tags.name';
      $params[] = 'tags.parentId';
    }
    $params = implode(', ', $params);
    $q = "
    SELECT $params
    FROM tags_items
    LEFT JOIN {$this->group->table} tags ON tags_items.tagId=tags.id
    WHERE
      tags_items.strName=? AND
      tags_items.groupName=? AND
      tags_items.itemId IN (".implode(', ', $itemIds).") AND
      tags_items.active=1
      ";
    $tagItems = db()->select($q, $this->strName, $this->group->name);
    if ($this->getRelatedItems and ($items = $this->group->getRelatedItems()) !== false) {
      foreach ($tagItems as &$v) $v = $items->getItemF($v['id']);
    }
    return $tagItems;
  }

  public $getRelatedItems = false;

  protected $parents;

  /**
   * Вставляет $node в нод с $parentId
   * Используется при сохранении древовидных тэгов
   *
   * @param $parentId
   * @param $node
   * @return mixed
   */
  protected function injectInParent($parentId, $node) {
    $parent = DbModelCore::get($this->group->table, $parentId)->r;
    $parent['childNodes'] = [$node];
    $parent['collection'] = $node['collection'];
    $this->parents = $parent;
    if ($parent['parentId']) $this->injectInParent($parent['parentId'], $parent);
    return $this->parents;
  }

  function getItems($itemId) {
    return $this->group->tree ? $this->getTree($itemId) : $this->getFlat($itemId);
  }

  /*
  static function updateCountByItemId($strName, $itemId) {
    if (self::$disableUpdateCount) return;
    $r = db()->query("SELECT groupName, tagId FROM tags_items WHERE strName=? AND itemId=?d GROUP BY tagId", $strName, $itemId);
    foreach ($r as $v) self::updateCount($v['tagId']);
  }

  static function activate($strName, $itemId) {
    db()->query("UPDATE tags_items SET active=1 WHERE strName=? AND itemId=?d", $strName, $itemId);
    self::updateCountByItemId($strName, $itemId);
  }

  static function deactivate($strName, $itemId) {
    db()->query("UPDATE tags_items SET active=0 WHERE strName=? AND itemId=?d", $strName, $itemId);
    self::updateCountByItemId($strName, $itemId);
  }
  */

  /**
   * Удаляет те тэги, к которым не было найдено ниодной ТэгЗаписи
   */
  static function cleanup() {
    db()->query('
      SELECT tags.id, tags_items.tagId AS exists FROM tags
      LEFT JOIN tags_items ON tags_items.tagId=tags.id');
  }

}