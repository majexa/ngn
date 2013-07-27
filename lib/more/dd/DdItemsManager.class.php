<?php

class DdItemsManager extends DbItemsManager {

  /**
   * Имя структуры текущих записей
   *
   * @var string
   */
  public $strName;

  /**
   * Массив типов полей текущей структуры
   *
   * @var array
   */
  public $types;

  /**
   * @var Image
   */
  public $oImage;

  /**
   * @var DdItemsPage
   */
  public $oItems;

  function __construct(DdItems $items, Form $form, array $options = []) {
    parent::__construct($items, $form, $options);
    $this->strName = $items->strName;
  }

  /**
   * Добавляет ID раздела в данные создаваемой записи
   *
   * @param   array   Данные создаваемой записи
   */
  protected function addCreateData() {
    parent::addCreateData();
    // Если статус активности не определён (а это значит, что пользователь просто не
    // может его редактировать и поле не было создано), назначаем его значение по умолчанию
    if (!isset($this->data['active'])) $this->data['active'] = $this->defaultActive;
    if ($this->authorId) $this->data['userId'] = $this->authorId;
  }

  function getAttacheFolder() {
    return DdCore::filesDir($this->strName).'/'.$this->id;
  }

  protected function beforeDelete() {
    if (($paths = Hook::paths('dd/beforeDelete')) !== false) foreach ($paths as $path) include $path;
    Dir::remove(UPLOAD_PATH.'/dd/'.$this->strName.'/'.$this->id);
  }

  function getTinyAttachItemId($itemId, $fieldName = '') {
    return 'dd-'.$this->strName.'-'.$itemId.'-'.$fieldName;
  }

  protected function allIds() {
    return db()->selectCol("SELECT id FROM {$this->oItems->table}");
  }

  function deleteAll() {
    foreach ($this->allIds() as $id) $this->delete($id);
  }

  protected function getItem($id) {
    return $this->oItems->getItemNonFormat($id);
  }

  protected function replaceData() {
    parent::replaceData();
    if (($paths = Hook::paths('dd/itemsManagerReplaceData')) !== false) foreach ($paths as $path) include $path;
  }

  static function getDefault($strName, array $options = []) {
    return new self(new DdItems($strName), new DdForm(new DdFields($strName), $strName), $options);
  }

}