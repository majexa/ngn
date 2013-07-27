<?php

class DdFormPageSlave extends DdFormPage {
  
  public $masterStrName;
  public $masterPageId;
  
  function __construct(DdFields $fields, $pageId, $masterStrName, $masterPageId,
  array $options = []) {
    parent::__construct($fields, $pageId);
    $this->masterStrName = $masterStrName;
    $this->masterPageId = $masterPageId;
  }
  
}
