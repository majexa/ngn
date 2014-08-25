<?php

/**
 * Structure generator for client-side Ngn.Tree class
 */
class ClientTree {

  /**
   * @var TreeInterface
   */
  protected $initTree;

  /**
   * @var array
   */
  protected $tree = [];

  protected $allowedDataParams = ['id' /*, 'title'*/];

  function __construct(TreeInterface $tree) {
    $this->initTree = $tree;
    $initNodes = [$this->initTree->getTree()];
    foreach ($initNodes as $initNode) {
      $node = $this->node($initNode);
      $this->setChildren($node, $initNode);
      $this->tree[] = $node;
    }
  }

  protected function setChildren(&$node, $initNode) {
    if (empty($initNode[$this->initTree->childrenKey()])) {
      $node['children'] = [];
      return;
    }
    foreach ($initNode[$this->initTree->childrenKey()] as $initChildNode) {
      $childNode = $this->node($initChildNode);
      $this->setChildren($childNode, $initChildNode);
      $node['children'][] = $childNode;
    }
  }

  function getTree() {
    return $this->tree;
  }

  protected function replaceNodes(array &$nodes) {
    foreach ($nodes as &$node) {
      $node = $this->node($node);
      if (!empty($node[$this->initTree->childrenKey()])) {
        $this->replaceNodes($node[$this->initTree->childrenKey()]);
      }
    }
  }

  protected function node(array $data = []) {
    if (empty($data['title'])) $data['title'] = '{empty}';
    $node = [
      'property' => [
        'id'   => $data['id'],
        'name' => $data['title']
      ]
    ];
    //$children = $data[$this->_tree->childrenKey()];
    $this->setNodeType($node, $data);
    $this->setNodeCls($node, $data);
    $node['data'] = Arr::filterByKeys($data, $this->allowedDataParams);
    //$node['children'] =
    return $node;
  }

  protected function setNodeType(array &$node, array $data) {
    $node['type'] = !empty($data[$this->initTree->childrenKey()]) ? 'folder' : 'page';
  }

  protected function setNodeCls(array &$node, array $data) {
  }

  /**
   * @param TreeInterface $tree
   * @param $table
   * @param integer $id
   * @param integer $toId
   * @param string $where
   * @param array $whereParams
   */
  static function move(TreeInterface $tree, $table, $id, $toId, $where = 'after', array $whereParams = null) {
    if ($where == 'inside') {
      $parentId = $toId;
      // Получаем последний OID
      $oid = db()->selectCell("
        SELECT oid FROM $table 
        WHERE ".db()->getAndCond($whereParams)." 
        ORDER BY oid DESC
        LIMIT 1");
      $oid++;
    }
    elseif ($where == 'before') {
      $parentId = $tree->getParentId($toId);
      $oid = db()->selectCell("SELECT oid FROM $table WHERE id=?d", $toId);
      $oid--;
    }
    else {
      $parentId = $tree->getParentId($id);
      $oid = db()->selectCell("SELECT oid FROM $table WHERE id=?d", $toId);
      $oid++;
    }
    db()->query("UPDATE $table SET parentId=?d WHERE id=?d", $parentId, $id);
    db()->query("UPDATE $table SET oid=?d WHERE id=?d", $oid, $id);
    $cond = (new DbCond)->addF('parentId', $parentId);
    foreach ($whereParams as $k => $v) $cond->addF($k, $v);
    DbShift::sort($table, $cond);
  }

}
