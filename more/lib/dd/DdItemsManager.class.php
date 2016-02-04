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
    $config = Config::getVar('dd/itemsManager.'.$items->strName, true) ?: Config::getVar('dd/itemsManager');
    $this->imageSizes = array_merge($this->imageSizes, //
      Arr::filterByKeys($config, array_keys($this->imageSizes)));
    Arr::toObjProp($config, $this);
    $this->strName = $items->strName;
  }

  protected function _afterUpdate() {
    parent::_afterUpdate();
    $this->replaceItemCache($this->id);
  }

  protected function _afterCreate() {
    parent::_afterCreate();
    $this->replaceItemCache($this->id);
  }

  protected function replaceItemCache($id) {
    $this->items->cc($id);
    $this->items->getItem_cache($id);
  }

  protected function _create() {
    $data = [];
    foreach ($this->data as $k => $v) {
      if (!($type = $this->form->fields->getType($k))) {
        $data[$k] = $v;
        continue;
      }
      // Для теговых полей данные сохраняются в табличку tagItems
      if (!DdTags::isTag($type)) $data[$k] = $v;
    }
    return $this->items->create($data);
  }

  protected function _update() {
    foreach ($this->data as $name => $v) {
      if (!($type = $this->form->fields->getType($name))) {
        if (!Misc::hasSuffix('From', $name) and !Misc::hasSuffix('To', $name)) continue;
      }
      else {
        if (DdTags::isTag($type)) continue;
      }
      $data[$name] = $v;
    }
    if (!empty($data)) $this->items->update($this->id, $data);
    else $this->items->updateField($this->id, 'dateUpdate', Date::db());
  }

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
    foreach (Hook::paths('dd/beforeDelete') as $path) include $path;
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
    foreach (Hook::paths('dd/itemsManagerReplaceData') as $path) include $path;
  }

  function dbUpdateFieldByKey($keyFieldName, $keyValue, $fieldName, $value) {
    $itemId = Misc::checkEmpty(db()->selectCell("SELECT id FROM dd_i_".$this->strName." WHERE $keyFieldName=?", $keyValue));
    db()->query("UPDATE dd_i_".$this->strName." SET $fieldName=? WHERE id=$itemId", $value);
    $this->replaceItemCache($itemId);
  }

}