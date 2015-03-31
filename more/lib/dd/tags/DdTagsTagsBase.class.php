<?php

abstract class DdTagsTagsBase {

  /**
   * @var DdTagsGroup
   */
  public $group;

  function __construct(DdTagsGroup $group) {
    $this->group = $group;
  }

  function create(array $data) {
    Arr::checkEmpty($data, 'title');
    $data['strName'] = $this->group->strName;
    $data['groupName'] = $this->group->name;
    return DbModelCore::create($this->group->table, $data);
  }

  function update($id, array $data) {
    return DbModelCore::update($this->group->table, $id, $data);
  }


  /**
   * @var DbCond
   */
  protected $cond;

  function getCond($strName) {
    if (isset($this->cond)) return $this->cond;
    $this->cond = DbCond::get();
    if ($this->group->allowEdit) $this->cond->setOrder('oid');
    if (!$this->group->global) {
      $this->cond->addF('groupName', $this->group->name);
      $this->cond->addF('strName', $strName);
    }
    return $this->cond;
  }

  function getSelectCond() {
    return $this->getCond($this->group->tagsGetterStrName);
  }

  function getUpdateCond() {
    return $this->getCond($this->group->strName);
  }

  function getParentId($id) {
    return db()->selectCell("SELECT parentId FROM {$this->group->table} WHERE id=?d", $id);
  }

  /**
   * Удаляет все теги текущей группы
   */
  function deleteAll() {
    db()->query("DELETE FROM {$this->group->table} WHERE strName=? AND groupId=?", $this->group->strName, $this->group->name);
    db()->query('DELETE FROM tagItems WHERE strName=? AND groupId=?', $this->group->strName, $this->group->name);
  }

  abstract function import($text);

  function notEmpty() {
    $this->getSelectCond()->addFromFilter('cnt', 1);
    return $this;
  }

  function search($text) {
    $text = str_replace('%', '', $text).'%';
    //return db()->query("SELECT * FROM {$this->group->table} ".$this->getSelectCond()->addLikeFilter('title', $text)->all()." ", $text);
    $cond = clone $this->getSelectCond();
    $cond->addLikeFilter('t1.title', $text);
    return db()->query("SELECT t1.*, t2.title AS parentTitle FROM {$this->group->table} AS t1 LEFT JOIN {$this->group->table} AS t2 ON t2.id=t1.parentId ".$cond->all()." ", $text);
  }

  abstract function getData();

  function getItem($id) {
    return DbModelCore::get($this->group->table, $id)->r;
  }

  function delete($id) {
    DbModelCore::delete($this->group->table, $id);
    db()->query('DELETE FROM tagItems WHERE tagId=?d', $id);
  }

  function getTagsByItemIds($itemIds) {
    $itemIds = (array)$itemIds;
    $cond = new DbCond('tagItems');
    $cond->addJoin($this->group->table, 'tagId');
    $cond->addF('itemId', $itemIds)->addF('strName', $this->group->tagsGetterStrName)->addF('groupId', $this->group->name);
    return db()->query("SELECT {$this->group->table}.*, COUNT(*) AS cnt FROM tagItems".$cond->all().' GROUP BY tagItems.tagId');
  }

}
