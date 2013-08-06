<?php

/**
 * Поля для формы dd-структуры
 */
class DdFields extends Fields {

  public $strName;

  protected function defineOptions() {
    $this->options['getHidden'] = false;
    $this->options['getSystem'] = false;
    $this->options['getDisallowed'] = false;
    $this->options['getVirtual'] = false;
  }

  function __construct($strName, array $options = []) {
    Misc::checkEmpty($strName);
    $this->strName = $strName;
    parent::__construct([], $options);
  }

  public $initFields = [];

  protected function addDdFieldsToInit() {
    $this->initFields = Arr::append($this->initFields, DbModelCore::collection('dd_fields', DbCond::get()->addF('strName', $this->strName)->setOrder('oid')));
  }

  protected function init() {
    if ($this->options['getHidden']) {
      $this->options['getSystem'] = true;
      $this->options['getDisallowed'] = true;
    }
    $this->initFields[] = [
      'title'           => 'ID',
      'name'            => 'id',
      'type'            => 'integer',
      'system'          => true,
      'defaultDisallow' => true
    ];
    $this->addDdFieldsToInit();
    $this->initFields[] = [
      'title'           => 'Дата создания',
      'name'            => 'dateCreate',
      'type'            => 'datetime',
      'system'          => true,
      'defaultDisallow' => false
    ];
    $this->initFields[] = [
      'title'           => 'Дата изменения',
      'name'            => 'dateUpdate',
      'type'            => 'datetime',
      'system'          => true,
      'defaultDisallow' => false
    ];
    $this->initFields();
  }

  function initFields() {
    $this->addFieldsWithFilter($this->initFields);
  }

  protected function addFieldsWithFilter(array $fields) {
    if (!$this->options['getSystem']) $fields = Arr::filterByValue($fields, 'system', 0);
    if (!$this->options['getDisallowed']) $fields = Arr::filterByValue($fields, 'defaultDisallow', 0);
    if (!$this->options['getVirtual']) $fields = Arr::filterByValue($fields, 'virtual', 0, false, true);
    foreach ($fields as &$v) {
      $v = Arr::filterEmptyStrings($v);
      $v['dd'] = true;
    }
    $this->addFields($fields);
  }

  protected function addInitFields() {}

  function addField(array $v) {
    $v['strName'] = $this->strName;
    if (isset($v['active'])) $v['active'] = 1;
    foreach (['system', 'defaultDisallow', 'virtual'] as $k) if (isset($v[$k])) $v[$k] = 0;
    parent::addField($v);
  }

  function exists($name) {
    return isset($this->initFields[$name]);
  }

  function getType($name) {
    $r = Arr::getSubValue($this->initFields, 'name', $name, 'type');
    return $r;
  }

  function getTagFields() {
    return array_filter($this->getFields(), function(&$v) {
      return DdTags::isTagType($v['type']);
    });
  }

  function getDateFields() {
    return array_filter($this->getFields(), function(&$v) {
      return ClassCore::hasAncestor(FieldCore::getClass($v['type']), 'FieldEDate');
    });
  }

  /**
   * @param string|array
   * @return array
   */
  function getFieldsByType($type) {
    $type = (array)$type;
    return array_filter($this->getFields(), function($v) use ($type) {
      return in_array($v['type'], $type);
    });
  }

}