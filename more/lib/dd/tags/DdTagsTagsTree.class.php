<?php

class DdTagsTagsTree extends DdTagsTagsBase implements TreeInterface {

  function getTree() {
    return db()->select("
    SELECT
      id,
      parentId,
      title,
      name,
      groupName,
      cnt,
      id       AS ARRAY_KEY,
      parentId AS PARENT_KEY
    FROM {$this->group->table}".$this->getSelectCond()->all());
  }

  function childrenKey() {
    return 'childNodes';
  }

  function getRoot() {
    throw new Exception('not implemented. change TreeInterface');
  }

  protected $curParentIds, $parentIds;

  function _getTree($parentId = null) {
    $tree = $this->getTree();
    if ($parentId !== null) {
      if (($node = $this->findNode($parentId, $tree))) return $node['childNodes'];
    }
    return $tree;
  }

  /**
   * Возвращает ID-шники родительских тэгов из указанных тэгов
   * Пример:
   * $this->getParentIds([3, 5])
   * вернёт:
   * [
   *   [
   *     51, 34, 3
   *   ],
   *   [
   *     43, 12, 6
   *   ]
   * ]
   *
   * @param array $ids ID-шники детей
   * @return array
   * @throws Exception
   */
  function getParentIds(array $ids) {
    if (!$ids) return [];
    try {
      foreach ($ids as $id) {
        $tree = $this->_getTree();
        if (!is_array($tree)) die2($tree);
        $this->setParentIds($tree, $id);
      }
    } catch (Exception $e) {
      throw new Exception($e->getMessage().'. $ids: '.getPrr($ids));
    }
    return array_values($this->parentIds);
  }

  function getParentIds2($id, $includeSelf = true) {
    $r = Arr::first($this->setParentIds($this->_getTree(), $id));
    if (!$includeSelf) $r = Arr::drop($r, $id);
    return $r;
  }

  /**
   * Производит поиск узла с указанным ID в дереве
   * Сохраняет в массив $this->parentIds все родительские ID и ID самого узла
   *
   * @param array $nodes Массив с деревом
   * @param integer $id ID искомого узла
   * @return mixed
   * @throws NotFoundException
   */
  private function setParentIds(array $nodes, $id) {
    Misc::checkNumeric($id);
    $this->curParentIds = [];
    $this->parentIds[$id] = [];
    $this->_setParentIds($nodes, $id);
    if (empty($this->parentIds[$id])) throw new NotFoundException("Tag ID=$id does not exists in tree");
    return $this->parentIds;
  }

  private function _setParentIds(array $nodes, $id) {
    foreach ($nodes as $node) {
      $this->curParentIds[] = $node['id'];
      if ($node['id'] == $id) {
        $this->parentIds[$id] = array_merge($this->parentIds[$id], $this->curParentIds);
        return;
      }
      if ($node['childNodes']) $this->_setParentIds($node['childNodes'], $id);
      array_pop($this->curParentIds);
    }
  }

  function getByTitle($title, $parentId) {
    return db()->selectRow('SELECT * FROM tags WHERE strName=? AND groupName=? AND title=? AND parentId=?d', $this->group->tagsGetterStrName, $this->group->name, $title, $parentId);
  }

  function getNodesTotalCount() {
    return db()->selectCell("SELECT COUNT(*) FROM {$this->group->table}".$this->getSelectCond()->all());
  }

  /**
   * @param integer|array $parentId Один или несколько идентификаторов
   * @return array
   */
  function getTags($parentId) {
    $cond = clone $this->getSelectCond();
    $cond->addF('parentId', $parentId);
    return db()->select("SELECT * FROM {$this->group->table}".$cond->all());
  }

  function import($text) {
    $oT2T = new Text2Tree;
    $oT2T->setText($text);
    $ids = [];
    $n = 10;
    foreach ($oT2T->getNodes() as $v) {
      $parent = (isset($v['parent']) and isset($ids[$v['parent']])) ? $ids[$v['parent']] : 0;
      $id = $this->create([
        'title'    => $v['title'],
        'parentId' => $parent,
        'oid'      => $n
      ]);
      $ids[$v['n']] = $id;
      $n += 10;
    }
  }

  function getData() {
    return $this->_getTree();
  }

  // ---------------------------

  protected $branchNodes;

  function getBranchFromRoot($childId) {
    $tree = $this->getTree();
    foreach ($tree as $node) {
      if ($node['id'] == $childId) return $this->getWithoutChildren($node);
      if (!empty($node['childNodes'])) {
        $this->branchNodes = [$this->getWithoutChildren($node)];
        if ($this->processBranch($node['childNodes'], $childId)) {
          for ($i = 1; $i < count($this->branchNodes); $i++) {
            $this->branchNodes[$i - 1]['childNodes'][] =& $this->branchNodes[$i];
          }
          return $this->branchNodes[0];
        }
      }
    }
    return false;
  }

  protected function getWithoutChildren(array $node) {
    $node['childNodes'] = [];
    return $node;
  }

  protected function processBranch(array $nodes, $childId) {
    foreach ($nodes as $node) {
      if ($node['id'] == $childId) {
        $this->branchNodes[] = $this->getWithoutChildren($node);
        return true;
      }
      if (!empty($node['childNodes'])) {
        $this->branchNodes[] = $this->getWithoutChildren($node);
        if ($this->processBranch($node['childNodes'], $childId)) return true;
        array_pop($this->branchNodes);
      }
    }
    return false;
  }

  // ---------------------------

  protected function getChildrenIds($parentId, $includeParent = true) {
    $node = $this->findNode($parentId, $this->_getTree());
    $ids = $includeParent ? [$parentId] : [];
    return Arr::append($ids, TreeCommon::flat($node['childNodes'], 'id'));
  }

  protected function findNode($id, $nodes) {
    foreach ($nodes as $node) {
      if ($node['id'] == $id) return $node;
      elseif (!empty($node['childNodes'])) {
        if (($_node = $this->findNode($id, $node['childNodes'])) !== false) {
          return $_node;
        }
      }
    }
    return false;
  }

  /**
   * @param integer $id Кого
   * @param integer $toId Куда
   * @param string $where После кого
   */
  function move($id, $toId, $where = 'after') {
    $oldParentTagIds = $this->getParentIds2($id, false);
    ClientTree::move($this, $this->group->table, $id, $toId, $where, [
      'strName'   => $this->group->strName,
      'groupName' => $this->group->name
    ]);
    $newParentTagIds = $this->getParentIds2($id, false);
    $tagIdsToDelete = array_diff($oldParentTagIds, $newParentTagIds);
    foreach ($tagIdsToDelete as $tagId) {
      DdTags::items($this->group->strName, $this->group->name)->deleteByTagId($tagId);
    }
  }

  function delete($id) {
    DbModelCore::delete($this->group->table, $id);
    db()->query('DELETE FROM tagItems WHERE tagId=?d', $id);
  }

}
