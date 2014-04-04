<?php

class DdoAdminFactory {

  /**
   * Массив с данными раздела
   *
   * @var array
   */
  protected $page;

  /**
   * Имя класса DdoPage
   *
   * @var string
   */
  protected $tplDdItemsModuleClass;

  /**
   * Имя класса DdoPage
   *
   * @var string
   */
  protected $tplDdItemsLayoutClass;

  function __construct($page) {
    $this->page = $page;
    if (!empty($page['module'])) $this->tplDdItemsModuleClass = PageModuleCore::getClass($page['module'], 'DdoApm');
    return $this;
  }

  function get() {
    if (isset($this->tplDdItemsModuleClass) and Lib::exists($this->tplDdItemsModuleClass)) {
      return eval('return new '.$this->tplDdItemsModuleClass.'($this->page, "adminItems");');
    }
    elseif (isset($this->tplDdItemsLayoutClass) and Lib::exists($this->tplDdItemsLayoutClass)) {
      return eval('return new '.$this->tplDdItemsLayoutClass.'($this->page, "adminItems");');
    }
    $o = new DdoAdmin($this->page['strName'], 'adminItems');
    return $o;
  }

}
