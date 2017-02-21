<?php

/**
 * Поля для формы dd-структуры
 */
class DdFields extends Fields {

  public $strName;


  /**
   * @param string $strName
   * @param array $options
   */
  function __construct($strName, array $options = []) {
    Misc::checkEmpty($strName, '$strName');
    $this->strName = $strName;
    parent::__construct([], $options);
  }

  protected function init() {
    if ($this->options['getHidden']) {
      $this->options['getSystem'] = true;
      $this->options['getDisallowed'] = true;
    }
    $this->addField([
      'title'           => 'ID',
      'name'            => 'id',
      'type'            => 'integer',
      'disableUpdate'   => true,
      'system'          => true,
      'defaultDisallow' => true
    ]);
    $this->addFields(DbModelCore::collection('dd_fields', DbCond::get()->addF('strName', $this->strName)->setOrder('oid')));
    $this->addField([
      'title'           => Locale::get('creationDate'),
      'name'            => 'dateCreate',
      'type'            => 'datetime',
      'system'          => true,
      'defaultDisallow' => false
    ]);
    $this->addField([
      'title'           => Locale::get('updateDate'),
      'name'            => 'dateUpdate',
      'type'            => 'datetime',
      'system'          => true,
      'defaultDisallow' => false
    ]);
    $this->addField([
      'title'           => Locale::get('author'),
      'name'            => 'userId',
      'type'            => 'user',
      'system'          => true,
      'defaultDisallow' => false
    ]);
  }

  public $initFields = [];

  function addField(array $v, $after = false) {
    $v['strName'] = $this->strName;
    $v['dd'] = true;
    if (!empty($v['settings'])) $v['settings'] = unserialize($v['settings']);
    if (isset($v['active'])) $v['active'] = 1;
    foreach (['system', 'defaultDisallow', 'virtual'] as $k) if (!isset($v[$k])) $v[$k] = 0;
    $this->initFields[$v['name']] = $v;
    parent::addField($v, $after);
  }

  function exists($name) {
    return isset($this->initFields[$name]);
  }

  function getType($name) {
    return isset($this->initFields[$name]) ? $this->initFields[$name]['type'] : false;
  }

  function getTagFields() {
    return array_filter($this->initFields, function (&$v) {
      return DdTags::isTag($v['type']);
    });
  }

  function getDateFields() {
    return array_filter($this->getFields(), function ($v) {
      return FieldCore::hasAncestor($v['type'], 'date');
    });
  }

  function getTextFields() {
    return array_filter($this->getFields(), function (&$v) {
      if (FieldCore::hasAncestor($v['type'], 'num')) return false;
      return FieldCore::hasAncestor($v['type'], 'text') or FieldCore::hasAncestor($v['type'], 'textarea');
    });
  }

  /**
   * @param string|array
   * @return array
   */
  function getFieldsByType($type) {
    $type = (array)$type;
    return array_filter($this->getFields(), function ($v) use ($type) {
      return in_array($v['type'], $type);
    });
  }

}