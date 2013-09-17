<?php

class DdTagsTagsTree extends DdTagsTagsBase {

  protected $curParentIds, $parentIds;

  /**
   * Возвращает ID-шники родительских тэгов из указанных тэгов
   *
   * @param   array   ID-шники детей
   * @return  array   Родительские ID-шники, группированые по ID-шникам детей
   *                  Пример:
   *                  $this->getParentIds([3, 5])
   *                  вернёт:
   *                  [
   *                    [
   *                      51, 34, 3
   *                    ],
   *                    [
   *                      43, 12, 6
   *                    ]
   *                  ]
   */
  function getParentIds(array $ids) {
    setProcessTimeStart();
    $tree = $this->_getTree();
    output("_getTree time: ".getProcessTime());
    if (!$ids) return [];
    setProcessTimeStart();
    $times = [];
    foreach ($ids as $id) {
      setProcessTimeStart(1);
      $this->setParentIds($tree, $id);
      $times[] = getProcessTime(1);
    }
    output('times: '.implode(', ', $times));
    output("foreach setParentIds: ".getProcessTime());
    return array_values($this->parentIds);
  }

  function getParentIds2($id, $includeSelf = true) {
    $tree = $this->_getTree();
    $this->setParentIds($tree, $id);
    $r = Arr::first($this->parentIds);
    if (!$includeSelf) $r = Arr::drop($r, $id);
    return $r;
  }

  /**
   * Производит поиск в дереве узла с указанным ID и сохраняет в массив $this->parentIds
   * все родительские ID и ID самого узла
   *
   * @param   array     Массив с деревом
   * @param   integer   ID искомого узла
   */
  private function setParentIds(array $nodes, $id) {
    Misc::checkNumeric($id);
    $this->curParentIds = [];
    $this->parentIds[$id] = [];
    $this->_setParentIds($nodes, $id);
    if (empty($this->parentIds[$id])) throw new Exception("Tag ID=$id does not exists in tree");
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

  function getTree($parentId = null) {
    $tree = $this->_getTree();
    if ($parentId !== null) {
      if (($node = $this->findNode($parentId, $tree))) return $node['childNodes'];
    }
    return $tree;
  }

  protected function _getTree() {
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

  /**
   * @param miexed Один или несколько идентификаторов
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
    return Arr::append($ids, TreeCommon::getFlatParams($node['childNodes'], 'id'));
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
   * @param integer Кого
   * @param integer Куда
   * @param integer После кого
   */
  function move($id, $toId, $where = 'after') {
    $oldParentTagIds = $this->getParentIds2($id, false);
    MifTree::move($this, $this->group->table, $id, $toId, $where, [
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
    db()->query('DELETE FROM tags_items WHERE tagId=?d', $id);
  }

}
