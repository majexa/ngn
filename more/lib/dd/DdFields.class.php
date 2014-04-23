<?php

/**
 * Поля для формы dd-структуры
 */
class DdFields extends Fields {

  public $strName;

  protected function defineOptions() {
    return [
      'getHidden'     => false,
      'getSystem'     => false,
      'getDisallowed' => false,
      'getVirtual'    => false
    ];
  }

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
      'system'          => true,
      'defaultDisallow' => true
    ]);
    $this->addFields(DbModelCore::collection('dd_fields', DbCond::get()->addF('strName', $this->strName)->setOrder('oid')));
    $this->addField([
      'title'           => 'Дата создания',
      'name'            => 'dateCreate',
      'type'            => 'datetime',
      'system'          => true,
      'defaultDisallow' => false
    ]);
    $this->addField([
      'title'           => 'Дата изменения',
      'name'            => 'dateUpdate',
      'type'            => 'datetime',
      'system'          => true,
      'defaultDisallow' => false
    ]);
  }

  function getFieldsF() {
    $fields = $this->getFields();
    if (!$this->options['getSystem']) $fields = Arr::filterByValue($fields, 'system', 0, true);
    if (!$this->options['getDisallowed']) $fields = Arr::filterByValue($fields, 'defaultDisallow', 0, true);
    if (!$this->options['getVirtual']) $fields = Arr::filterByValue($fields, 'virtual', 0, true, true);
    foreach ($fields as &$v) $v = Arr::filterEmptyStrings($v);
    return $fields;
  }

  public $initFields = [];

  function addField(array $v, $after = false) {
    $v['strName'] = $this->strName;
    $v['dd'] = true;
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
    return array_filter($this->getFields(), function (&$v) {
      return ClassCore::hasAncestor(FieldCore::getClass($v['type']), 'FieldEDate');
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