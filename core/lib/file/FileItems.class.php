<?php

class FileItems extends ArrayAccesseble implements UpdatableItems {

  protected $file;

  function __construct($file) {
    $this->file = $file;
    $this->init();
  }

  protected function init() {
    $this->r = $this->getItems();
  }

  function getItem($id) {
    return Arr::getValueByKey($this->getItems(), 'id', $id);
  }

  function getItems() {
    return FileVar::getVar($this->file, true) ?: [];
  }

  function create(array $data) {
    if (($items = $this->getItems())) {
      $data['id'] = Arr::last($items)['id']+1;
    }
    else $data['id'] = 1;
    $items = $this->getItems();
    $items[] = $data;
    FileVar::updateVar($this->file, $items);
    $this->init();
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
    FileVar::updateVar($this->file, $items);
    $this->init();
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
    FileVar::updateVar($this->file, array_values($items), true);
    $this->init();
  }

  protected function mergeItem(&$item, $data) {
    $item = array_merge($item, $data);
  }

  function delete($id) {
    FileVar::updateVar($this->file, array_values(Arr::dropBySubKeys($this->getItems(), 'id', $id)));
    $this->init();
  }

}