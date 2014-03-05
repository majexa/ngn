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
  public $image;

  /**
   * @var DdItems
   */
  public $items;

  /**
   * @var DdForm
   */
  public $form;

  function __construct(DdItems $items, Form $form, array $options = []) {
    parent::__construct($items, $form, $options);
    $this->strName = $items->strName;
  }

  protected function _afterUpdate() {
    parent::_afterUpdate();
    $this->createItemCache($this->id);
  }

  protected function _afterCreate() {
    parent::_afterCreate();
    $this->createItemCache($this->id);
  }

  protected function createItemCache($id) {
    $this->items->getItem_cache($id);
  }

  protected function _create() {
    $data = [];
    foreach ($this->data as $k => $v) {
      if (!($type = $this->form->fields->getType($k))) {
        $data[$k] = $v;
        continue;
      }
      if (!DdTags::isTag($type)) $data[$k] = $v;
    }
    return $this->items->create($data);
  }

  protected function _update() {
    foreach ($this->data as $k => $v) {
      if (!($type = $this->form->fields->getType($k))) continue;
      if (DdTags::isTag($type)) continue;
      $data[$k] = $v;
    }
    if (!empty($data)) $this->items->update($this->id, $data);
    else $this->items->updateField($this->id, 'dateUpdate', dbCurTime());
  }

  /**
   * Добавляет ID раздела в данные создаваемой записи
   *
   * @param   array Данные создаваемой записи
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
    return db()->selectCol("SELECT id FROM {$this->items->table}");
  }

  function deleteAll() {
    foreach ($this->allIds() as $id) $this->delete($id);
  }

  function getItem($id) {
    return $this->items->getItemNonFormat($id);
  }

  protected function replaceData() {
    parent::replaceData();
    if (($paths = Hook::paths('dd/itemsManagerReplaceData')) !== false) foreach ($paths as $path) include $path;
  }

}