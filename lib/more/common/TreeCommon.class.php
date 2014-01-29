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

  static function getFlatParams(array $tree, $param = null) {
    $params = [];
    foreach ($tree as $node) {
      $params[] = $param ? $node[$param] : Arr::filterByExceptKeys($node, 'childNodes');
      if (!empty($node['childNodes'])) $params = Arr::append($params, self::getFlatParams($node['childNodes'], $param));
    }
    return $params;
  }

  static function getFlatDddd(array $tree, $dddd) {
    $params = [];
    foreach ($tree as $node) {
      $params[] = St::dddd($dddd, $node);
      if (!empty($node['childNodes'])) $params = Arr::append($params, self::getFlatDddd($node['childNodes'], $dddd));
    }
    return $params;
  }

  /*
  static protected $id;
  static protected $parentId;
  static $idName = 'n';
  static $parentIdName = 'parent';
  static protected $result;

  static function getFlatAddParentIds(array $tree) {
    self::$parentId = 0;
    self::$id = 0;
    self::$result = [];
    self::setFlatAddParentIds($tree);
    return self::$result;
  }

  static protected function setFlatAddParentIds(array $nodes) {
    foreach ($nodes as $v) {
      self::$id++;
      $v[self::$parentIdName] = self::$parentId;
      $v[self::$idName] = self::$id;
      self::$result[] = $v;
      if (!empty($v['children'])) {
        $parentId = self::$parentId;
        self::$parentId = self::$id;
        self::setFlatAddParentIds($v['children']);
        self::$parentId = $parentId;
      }
    }
  }
  */

}
