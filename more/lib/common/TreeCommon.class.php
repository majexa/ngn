<?php

class TreeCommon {

  static function buildTree(array &$nodes, $parentId = 0) {
    $branch = array();
    foreach ($nodes as $node) {
      if ($node['parentId'] == $parentId) {
        $children = self::buildTree($nodes, $node['id']);
        if ($children) {
          $node['childNodes'] = $children;
        }
        $branch[$node['id']] = $node;
        unset($nodes[$node['id']]);
      }
    }
    return $branch;
  }

  static function lastInBranch(array $node) {
    if (!empty($node['childNodes'])) {
      return self::lastInBranch(Arr::first($node['childNodes']));
    }
    return $node;
  }

  static function flat(array $nodes, $param = null) {
    $params = [];
    if (!isset($nodes[0])) throw new Exception('param #1 ($nodes) must be the set of nodes');
    foreach ($nodes as $node) {
      $params[] = $param ? $node[$param] : Arr::filterByExceptKeys($node, 'childNodes');
      if (!empty($node['childNodes'])) $params = Arr::append($params, self::flat($node['childNodes'], $param));
    }
    return $params;
  }

  static function flatDddd(array $tree, $dddd) {
    $params = [];
    foreach ($tree as $node) {
      $params[] = St::dddd($dddd, $node);
      if (!empty($node['childNodes'])) $params = Arr::append($params, self::flatDddd($node['childNodes'], $dddd));
    }
    return $params;
  }

}
