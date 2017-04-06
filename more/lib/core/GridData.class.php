<?php

class GridData extends ArrayAccesseble {
  use Options;

  protected $fields, $items;

  protected function defineOptions() {
    return [
      'id' => 'id'
    ];
  }

  function __construct(Fields $fields, UpdatableItems $items, $options = []) {
    $this->setOptions($options);
    $this->fields = $fields;
    $this->items = $items;
    $this->r = $this->data();
  }

  protected function items() {
    return $this->items;
  }

  protected function body() {
    $r = [];
    foreach ($this->items() as $data) {
      $r['id'] = $data[$this->options['id']];
      $r['data'] = $data;
    }
    return $r;
  }

  protected function fields() {
    return $this->fields;
  }

  protected function data() {
    $grid['head'] = Arr::get(array_map(function ($v) {
      if (FieldCore::isBoolType($v['type'])) $v['title'] = '';
      return $v;
    }, $this->fields()->getFields()), 'title');
    $grid['body'] = $this->body();
    return $grid;
  }

}