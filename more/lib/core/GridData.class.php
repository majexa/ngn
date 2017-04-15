<?php

class GridData extends ArrayAccesseble {
  use Options;

  protected $_fields, $fields, $items;

  protected function defineOptions() {
    return [
      'id' => 'id'
    ];
  }

  function __construct(Fields $fields, UpdatableItems $items, $options = []) {
    $this->setOptions($options);
    $this->_fields = $fields;
    $this->items = $items;
    $this->fields = array_filter($this->fields()->getFields(), function($field) {
      return empty(FieldCore::get($field['type'], $field)['noValue']);
    });
    $this->r = $this->data();
  }

  protected function items() {
    return $this->items;
  }

  protected function body() {
    $items = [];
    $filter = array_keys($this->fields);
    foreach ($this->items() as $data) {
      $item['id'] = $data[$this->options['id']];
      $item['data'] = Arr::filterByKeys($data, $filter);
      $items[] = $item;
    }
    return $items;
  }

  protected function fields() {
    return $this->_fields;
  }

  protected function data() {
    $grid['head'] = Arr::get(array_map(function ($v) {
      if (FieldCore::isBoolType($v['type'])) $v['title'] = '';
      return $v;
    }, $this->fields), 'title');
    $grid['body'] = $this->body();
    return $grid;
  }

}