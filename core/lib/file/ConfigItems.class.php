<?php

class ConfigItems implements UpdatableItems {

  public $name;

  function __construct($name) {
    $this->name = $name;
  }

  function getItem($id) {
    return Arr::getValueByKey($this->getItems(), 'id', $id);
  }

  function getItems() {
    return Config::getVar($this->name, true, false) ?: [];
  }

  function create(array $data) {
    if (($items = $this->getItems())) {
      $data['id'] = Arr::last($items)['id']+1;
    }
    else $data['id'] = 1;
    $items = $this->getItems();
    $items[] = $data;
    ProjectConfig::updateVar($this->name, $items, true);
    return $data['id'];
  }

  function remove($id, $key, $subKey = false) {
    $items = $this->getItems();
    foreach ($items as &$item) {
      if ($item['id'] == $id) {
        if ($subKey) unset($item[$subKey][$key]);
        else unset($item[$key]);
      }
    }
    if (!isset($item)) throw new Exception("id '$id' not found");
    ProjectConfig::updateVar($this->name, $items, true);
  }

  function update($id, array $data, $replace = false) {
    $items = $this->getItems();
    foreach ($items as &$item) {
      if ($item['id'] == $id) {
        if ($replace) foreach ($data as $k => $v) $item[$k] = $v;
        else $this->mergeItem($item, $data);
      }
    }
    if (!isset($item)) throw new Exception("id '$id' not found");
    ProjectConfig::updateVar($this->name, array_values($items), true);
  }

  protected function mergeItem(&$item, $data) {
    $item = array_merge($item, $data);
  }

  function delete($id) {
    ProjectConfig::updateVar($this->name, array_values(Arr::dropBySubKeys($this->getItems(), 'id', $id)), true);
  }

}