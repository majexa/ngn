<?php

class Fields extends ArrayAccesseble {
use Options;

  protected function &getArrayRef() {
    return $this->fields;
  }

  /**
   * Пример массива:
   * array(
   *   'name' => 'fieldName',
   *   'title' => 'Название поля',
   *   'type' => 'select',
   *   'descr' => 'Это поле - совсем не поле, а лужайка непаханая',
   *   'maxlength' => 255,
   *   'required' => 1,
   *   'options' => array(
   *     1, 2, 3
   *   )
   * )
   *
   */
  public $fields = [];
  
  protected $n = 1;

  function __construct(array $fields = [], array $options = []) {
    $this->setOptions($options);
    $this->init();
    $this->addFields($fields);
  }

  protected function init() {}

  protected function defineOptions() {
    $this->options = [
      'errorOnTypeNotExists' => false
    ];
  }
  
  function addFields(array $fields) {
    foreach ($fields as $v) $this->addField($v);
  }

  function getFields() {
    return $this->fields;
  }

  function getFieldsF() {
    return $this->getFields();
  }
  
  function getInputFields() {
    return Arr::filterFunc($this->fields, function($v) {
      return FieldCore::isInput($v['type']);
    });
  }
  
  function getType($name) {
    return isset($this->fields[$name]['type']) ? $this->fields[$name]['type'] : false;
  }
  
  function getTypes() {
    return Arr::get($this->getFields(), 'type', 'name');
  }
  
  /**
   * Возвращает только обязательные для заполнения поля
   *
   * @return array Массив с данными полей
   */
  function getRequired() {
    $fields = [];
    foreach ($this->getFields() as $k => $v) {
      if (!empty($v['required'])) {
        $fields[$k] = $v;
      }
    }
    return $fields;
  }
  
  function getTitle($name) {
    return $this->fields[$name]['title'];
  }
  
  function getField($name) {
    return $this->fields[$name];
  }
  
  function addField(array $v) {
    if (!isset($v['name'])) $v['name'] = 'fld'.$this->n;
    if (!isset($v['type'])) $v['type'] = 'text';
    $this->fields[$v['name']] = $v;
    $this->n++;
  }

  function getFieldsByAncestor($ancestorType) {
    $fields = [];
    foreach ($this->getFields() as $k => $v) {
      if (FieldCore::hasAncestor($v['type'], $ancestorType))
        $fields[$k] = $v;
    }
    return $fields;
  }
  
  function getFileFields() {
    return $this->getFieldsByAncestor('file');
  }
   
  function getDateFields() {
    return $this->getFieldsByAncestor('date');
  }
  
  function exists($name) {
    return isset($this->fields[$name]);
  }
  
  function isFileType($name) {
    return $this->hasAncestor($name, 'file');
  }
  
  function hasAncestor($name, $ancestorType) {
    return FieldCore::hasAncestor($this->fields[$name]['type'], $ancestorType);
  }
  
  static function keyAsName(array $fields) {
    foreach ($fields as $k => &$v) $v['name'] = $k;
    return $fields;
  }

  static function defaults(array $types) {
    $r = [];
    foreach ($types as $v) {
      $class = 'FieldE'.ucfirst($v);
      $r[] = [
        'title' => $class::$title,
        'type' => $v,
        'name' => $v
      ];
    }
    return $r;
  }
  
}
