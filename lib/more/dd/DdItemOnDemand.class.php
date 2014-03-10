<?php

class DdItemOnDemand extends ArrayAccessebleOptions {

  public $onDemandFields = [];

  protected $strName;

  function __construct($strName, array $item) {
    $this->strName = $strName;
    parent::__construct($item);
  }

  protected $defined = [];

  function offsetGet($fieldName) {
    if (in_array($fieldName, $this->onDemandFields)) {
      if (isset($this->defined[$fieldName])) return $this->options[$fieldName];
      $this->defined[$fieldName] = true;
      $this->options[$fieldName] = $this->getData($fieldName);
      return $this->options[$fieldName];
    }
    return $this->options[$fieldName];
  }

  protected function getData($fieldName) {
    $r = [];
    foreach (DdTags::items($this->strName, $fieldName)->getTree($this->options['id']) as $node) {
      $r[$node['collection']] = TreeCommon::flat([$node]);
    }
    return $r;
  }

}

