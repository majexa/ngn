<?php

class DdFieldsManager extends DbItemsManager {

  public $strName;

  /**
   * @var DdFieldItems
   */
  public $items;

  /**
   * @var DbModel
   */
  protected $str;

  /**
   * @var DdFieldsManager
   */
  protected $filterFm;

  /**
   * @param string $strName
   * @param array $options
   */
  function __construct($strName, array $options = []) {
    Misc::checkEmpty($strName);
    $this->str = DbModelCore::get('dd_structures', $strName, 'name');
    $this->strName = $strName;
    $items = O::gett('DdFieldItems', $strName);
    parent::__construct($items, new DdFieldsForm(O::gett('DdFieldsFields', $this->strName), $this->strName, ['filterable' => !empty($this->str['filterStrName'])]), $options);
    if ($this->str['filterStrName']) {
      $this->filterFm = new DdFieldsManagerFilter($this->str['filterStrName'], [
        'strict' => false
      ]);
    }
  }

  protected function beforeFormElementsInit() {
    if (!empty($this->defaultData)) {
      $type = DdFieldCore::getTypeData($this->defaultData['type']);
      if (!empty($type['disableTypeChange'])) unset($this->form->fields->fields['type']);
      if (!empty($type['fields'])) {
        foreach ($type['fields'] as &$v) $v['name'] = "settings[{$v['name']}]";
        $this->form->fields->fields = array_merge($this->form->fields->fields, Arr::assoc($type['fields'], 'name'));
      }
    }
  }

  protected function replaceData() {
    parent::replaceData();
    if (!empty($this->data['type'])) {
      // Заменяем значения из формы, дозволеными статическими значениями из типа поля
      $this->data = array_merge($this->data, Arr::filterByKeys(DdFieldCore::getTypeData($this->data['type']), [
        'notList', 'system'
      ]));
    }
    $this->data['filterable'] = $this->filterable();
    $this->data['strName'] = $this->strName;
  }

  protected function dbCreateField() {
    if (!empty($this->data['virtual'])) return;
    $type = DdFieldCore::getTypeData($this->data['type']);
    if (!isset($type['dbLength'])) $type['dbLength'] = null;
    $this->_dbCreateField($type);
  }

  protected function _dbCreateField(array $type) {
    if (DdFieldCore::isRangeType($this->data['type'])) {
      $this->__dbCreateField($this->data['name'].'From', $type['dbType'], $type['dbLength'], $this->getCharsetCond($type));
      $this->__dbCreateField($this->data['name'].'To', $type['dbType'], $type['dbLength'], $this->getCharsetCond($type));
    }
    else {
      $this->__dbCreateField($this->data['name'], $type['dbType'], $type['dbLength'], $this->getCharsetCond($type));
      if (DdFieldCore::isFormatType($this->data['type'])) {
        // Если поле - форматируемое
        $this->__dbCreateField($this->data['name'].'_f', $type['dbType'], $type['dbLength'], $this->getCharsetCond($type));
      }
    }
  }

  /**
   * Добавляет новое поле в структуру таблицы
   *
   * @param string  Имя структуры
   * @param string  Имя поля
   * @param string  Тип
   * @param string  Максимальная длина значения
   * @param string  Кодировка
   */
  protected function __dbCreateField($name, $type, $length, $charsetCond, $default = null) {
    $this->__dbFieldAction("ADD $name", $type, $length, $charsetCond, $default);
  }

  protected function beforeCreate() {
    $this->dbCreateField();
  }

  protected function beforeUpdate() {
    $this->dbUpdateField();
  }

  protected function afterCreate() {
    $groupId = null;
    if (DdTags::isTagType($this->data['type'])) {
      $groupId = DdTagsGroup::create($this->strName, $this->data['name'], DdTags::isTagItemsDirectedType($this->data['type']), true, DdTags::isTagTreeType($this->data['type']));
    }
    $this->createFilter();
    $this->typeAction($this->data['type'], 'updateCreate', $this->data['name']);
    return $groupId;
  }

  protected function afterUpdate() {
    if ($this->form->getElement('name')->valueChanged) {
      $this->renameImages($this->beforeUpdateData['name'], $this->data['name']);
      $this->renameDdo($this->beforeUpdateData['name'], $this->data['name']);
    }
    if (DdTags::isTagType($this->data['type'])) {
      if (!DdTagsGroup::getData($this->strName, $this->beforeUpdateData['name'], false)) {
        // Если тэг-группы ещё не существовало
        DdTagsGroup::create($this->strName, $this->data['name'], DdTags::isTagItemsDirectedType($this->data['type']), true, DdTags::isTagTreeType($this->data['type']));
      }
      else {
        DdTagsGroup::update($this->strName, $this->beforeUpdateData['name'], $this->data['name'], DdTags::isTagItemsDirectedType($this->data['type']), true, DdTags::isTagTreeType($this->data['type']));
      }
    }
    $this->updateFilter();
    if ($this->form->getElement('type')->valueChanged) {
      $this->typeAction($this->data['type'], 'delete', $this->beforeUpdateData['name']);
      $this->typeAction($this->data['type'], 'updateCreate', $this->data['name']);
    }
  }

  protected function typeAction($type, $action, $name) {
    $class = 'Ddfma'.ucfirst($type);
    if (!class_exists($class)) return;
    $o = O::get($class, $this->strName);
    if (!method_exists($o, $action)) return;
    $o->$action($name);
  }

  protected function getCharsetCond(array $type) {
    if ($type['dbType'] == 'VARCHAR' or
      $type['dbType'] == 'TEXT' or
      $type['dbType'] == 'LONGTEXT'
    ) {
      return 'CHARACTER SET '.db()->charset.' COLLATE '.db()->collate;
    }
    else {
      return '';
    }
  }

  protected $currentType, $newType, $defaultDbValue;

  protected function initUpdateDbValues() {
    $this->currentType = DdFieldCore::getTypeData($this->beforeUpdateData['type']);
    $this->newType = DdFieldCore::getTypeData($this->data['type']);
    $this->defaultDbValue = empty($this->data['default']) ? null : $this->data['default'];
  }

  protected function dbFieldChanged() {
    if (DdFieldCore::isRangeType($this->data['type'])) {
      $this->_dbUpdateFieldChange($this->beforeUpdateData['name'].'From', $this->data['name'].'From');
      $this->_dbUpdateFieldChange($this->beforeUpdateData['name'].'To', $this->data['name'].'To');
    }
    else {
      $this->_dbUpdateFieldChangeDefault();
    }
    if (DdFieldCore::isFormatType($this->data['type'])) {
      // НОВЫЙ тип - форматируемый
      if (!DdFieldCore::isFormatType($this->beforeUpdateData['type'])) {
        // ТЕКУЩИЙ тип - не форматируемый
        // Если тип поля до этого был неформатируемый, а стал форматируемый, т.е.
        // ф-поле до этого не существовало
        $this->__dbCreateField($this->data['name'].'_f', $this->newType['dbType'], empty($this->newType['dbLength']) ? null : $this->newType['dbLength'], $this->getCharsetCond($this->newType), $this->defaultDbValue);
      }
      else {
        // Если поле и раньше было форматируемого типа, апдейтим
        $this->_dbUpdateFieldChange($this->beforeUpdateData['name'].'_f', $this->data['name'].'_f');
        $this->_dbUpdateFieldChange($this->beforeUpdateData['name'].'_f', $this->data['name'].'_f');
      }
    }
    elseif (DdFieldCore::isFormatType($this->beforeUpdateData['type'])) {
      $this->dbDeleteField($this->beforeUpdateData['name'].'_f');
    }
  }

  protected function dbUpdateField() {
    if (!empty($this->data['virtual'])) return;
    $this->initUpdateDbValues();
    if ((empty($currentType['disableTypeChange']) and $this->beforeUpdateData['type'] != $this->data['type']) or $this->beforeUpdateData['name'] != $this->data['name'] or $this->beforeUpdateData['default'] != $this->data['default']) {
      $this->dbFieldChanged();
    }
  }

  protected function _dbUpdateFieldChangeDefault() {
    $this->_dbUpdateFieldChange($this->beforeUpdateData['name'], $this->data['name']);
  }

  protected function _dbUpdateFieldChange($oldName, $newName) {
    $this->__dbUpdateFieldChange($this->beforeUpdateData['name'], $this->data['name'], $this->newType['dbType'], empty($this->newType['dbLength']) ? null : $this->newType['dbLength'], $this->getCharsetCond($this->newType), $this->defaultDbValue);
  }

  /**
   * Изменяет поле в таблице структуры
   *
   * @param   string  Имя структуры
   * @param   string  Старое имя поля
   * @param   string  Новое имя поля
   * @param   string  Тип поля
   * @param   string  Длина поля
   * @param   string  Кодировки
   */
  protected function __dbUpdateFieldChange($oldName, $newName, $type, $length, $charsetCond, $default = null) {
    $this->__dbFieldAction("CHANGE $oldName $newName", $type, $length, $charsetCond, $default);
  }

  protected function __dbFieldAction($word, $type, $length, $charsetCond, $default = null) {
    if (in_array(strtolower($type), ['int', 'float'])) $default = (int)$default;
    $q = "ALTER TABLE ".DdCore::table($this->strName)." $word $type ".($length ? '('.$length.')' : '')." $charsetCond NULL".($default !== null ? " DEFAULT '$default'" : '');
    LogWriter::str('queryes', $q);
    db()->query($q);
  }

  protected function dbDeleteField($fieldName) {
    db()->deleteCol(DdCore::table($this->strName), $fieldName, true);
  }

  protected function beforeDelete() {
    if (!empty($this->data['virtual'])) return;
    if (!DdFieldCore::isRangeType($this->data['type'])) $this->dbDeleteField($this->data['name']);
    if (!DdFieldCore::typeExists($this->data['type'])) return;
    // Если поле - форматируемое
    if (DdFieldCore::isFormatType($this->data['type'])) {
      $this->dbDeleteField($this->data['name'].'_f');
    }
    elseif (DdFieldCore::isRangeType($this->data['type'])) {
      $this->dbDeleteField($this->data['name'].'From');
      $this->dbDeleteField($this->data['name'].'To');
    }
    elseif (DdTags::isTagType($this->data['type'])) {
      (new DdTagsGroup($this->strName, $this->data['name']))->delete();
    }
    $this->deleteFilter();
    $this->typeAction($this->data['type'], 'delete', $this->data['name']);
  }

  protected function renameImages($oldFieldName, $newFieldName) {
    $strDir = UPLOAD_PATH.'/dd/'.$this->strName;
    if (!file_exists($strDir)) return;
    foreach (Dir::get($strDir) as $itemDir) {
      foreach (Dir::get($strDir.'/'.$itemDir) as $fileName) {
        if (!preg_match('/^(sm_|md_|)'.$oldFieldName.'(\.jpg)$/', $fileName)) continue;
        $newFileName = preg_replace('/^(sm_|md_|)'.$oldFieldName.'(\.jpg)$/', '$1'.$newFieldName.'$2', $fileName);
        rename($strDir.'/'.$itemDir.'/'.$fileName, $strDir.'/'.$itemDir.'/'.$newFileName);
      }
    }
  }

  protected function renameDdo($oldFieldName, $newFieldName) {
    //if (($page = DbModelCore::get('pages', $this->strName, 'strName')) === false) return;
    //(new DdoPageSettings($page))->renameField($oldFieldName, $newFieldName);
    //(new DdoSettings($this->strName))->
  }

  /**
   * Удаляет все поля структуры
   *
   * @param   string   Имя структуры
   */
  function deleteFields() {
    foreach (db()->ids('dd_fields', DbCond::get()->addF('strName', $this->strName)) as $id) {
      try {
        $this->delete($id);
      } catch (Exception $e) {
      }
    }
  }

  function deleteByName($name) {
    $this->delete(db()->selectCell("SELECT id FROM dd_fields WHERE strName=? AND name=?", $this->strName, $name));
  }

  function createIfNotExists(array $data) {
    if ($this->items->getItemByField('name', $data['name'])) return false;
    return $this->create($data);
  }

  protected function filterable() {
    return !(!isset($this->filterFm) or empty($this->data['filterable']));
  }

  protected function createFilter() {
    if (!$this->filterable()) return;
    $this->filterFm->create($this->data);
  }

  protected function updateFilter() {
    if (!$this->filterable()) return;
    $filterField = $this->filterFm->items->getItemByField('name', $this->beforeUpdateData['name']);
    if ($filterField) $this->filterFm->update($filterField['id'], $this->data);
  }

  protected function deleteFilter() {
    if (!$this->filterable()) return;
    if (!isset($this->filterFm) or empty($this->data['filterable'])) return;
    $this->filterFm->deleteByName($this->data['name']);
  }

  protected $typeCount = [];

  function create(array $d, $throwFormErrors = true) {
    Arr::incr($this->typeCount, $d['type']);
    if (!isset($d['name'])) $d['name'] = $d['type'].$this->typeCount[$d['type']];
    if (!isset($d['title'])) $d['title'] = $d['name'];
    return parent::create($d);
  }

}