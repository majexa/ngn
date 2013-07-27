<?php

class DdFormPage extends DdForm {

  /**
   * ID раздела формы
   *
   * @var integer
   */
  public $pageId;

  function __construct(Fields $fields, $pageId, array $options = []) {
    $this->pageId = $pageId;
    parent::__construct($fields, DbModelCore::get('pages', $this->pageId)->r['strName'], $options);
    if (($paths = Hook::paths('dd/formInit')) !== false) foreach ($paths as $path) include $path;
  }

}