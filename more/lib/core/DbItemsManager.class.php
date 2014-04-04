<?php

class DbItemsManager extends ItemsManager {

  /**
   * @var DbItems
   */
  public $items;
  
	function __construct(DbItems $items, Form $form, array $options = []) {
    parent::__construct($items, $form, $options);
  }
  
  function getAttacheFolder() {
    return 'tbl/'.$this->items->table.'/'.$this->id;
  }
  
  function setAuthorId($id) {
    parent::setAuthorId($id);
    $this->items->eventUserId = $id;
    return $this;
  }
  
  protected $oidAddMode = false;
  
  /**
   * Включает/выключает режим добавления oid'а к данным новой записи
   * 
   * @param   bool
   */
  function setOidAddMode($flag) {
    $this->oidAddMode = $flag;
  }
  
  /**
   * Добавляет в массив с данными из формы, дополнительные значения:
   * ID пользователя, если он залогинен
   *
   * @param   array   Данные создаваемой записи
   */
  protected function addCreateData() {
    parent::addCreateData();
    if ($this->oidAddMode) {
      $lastTableOid = db()->selectCell('SELECT oid FROM '.$this->items->table.' ORDER BY oid DESC LIMIT 1');
      $this->data['oid'] = $lastTableOid + 10;
    }
  }

}