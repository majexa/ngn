<?php

/**
 * @property int $id                Уникальный идентификатор группы
 * @property string $strName        Имя структуры группы
 * @property string $title          Название группы (поля)
 * @property string $name           Имя группы тэгов
 * @property string $fieldType      Тип поля, привязанного к этой группе
 * @property bool $unicalTagsName   Флаг определяет то, что имена тэгов этой группы должны быть уникальны
 * @property bool $tree             Флаг определяет то, что тэги этой группы могут иметь древовидную структуру
 * @property bool $itemsDirected    Флаг определяет то, что тэги этой группы управляются записыми,т.е. могут создаваться при создании записи с несуществующим тэгом
 * @property bool $multi            Флаг определяет, можно ли выбирать один тег или несколько
 * @property string $global         Не читывать strName и name при выборке
 * @property string $table          Таблица БД для этой группы
 * @property string $allowEdit      Разрешать редактирование
 * @property string $tagsGetterStrName Имя группы, используемое для получения тэгов
 * @property-private string $masterStrName  Имя мастер-структуры группы, Мастер-структура - та, которая отвечает за получение тэгов
 */
class DdTagsGroup {
  use ObjectPropertyGetter;

  function __construct($strName, $name) {
    Misc::checkEmpty($strName, '$strName');
    Misc::checkEmpty($name, '$name');
    // try {
    $this->p = self::getData($strName, $name);
    // } catch (Exception $e) {
    // throw new Exception("tag group $strName::$name does not exists");
    // }
    if (empty($this->p['fieldType'])) throw new Exception('Field for tag "'.$name.'" of "'.$strName.'" structure does not exists');
    $this->p['tagsGetterStrName'] = empty($this->p['masterStrName']) ? $this->p['strName'] : $this->p['masterStrName'];
    $this->p['tree'] = DdTags::isTree($this->p['fieldType']);
    $this->p['multi'] = DdTags::isMulti($this->p['fieldType']);
    $this->p['global'] = false;
    $this->p['table'] = 'tags';
    $this->p['allowEdit'] = true;
    $this->p = array_merge($this->p, $this->getTypeProperties());
  }

  function getTypeProperties() {
    $p = [];
    $field = (new DdFields($this->strName, ['getHidden' => true]))->getField($this->p['name']);
    if (DdTags::isDdItems($this->p['fieldType'])) {
      if (!empty($field['settings']['strName'])) {
        $strName = $field['settings']['strName'];
        $p['table'] = DdCore::table($strName);
      }
      $p['global'] = true;
      $p['allowEdit'] = false;
    }
    elseif ((FieldCore::hasAncestor($this->p['fieldType'], 'ddCity') or FieldCore::hasAncestor($this->p['fieldType'], 'ddCityMultiselect'))) {
      $p['global'] = true;
      $p['table'] = 'tagCities';
    }
    elseif ((FieldCore::hasAncestor($this->p['fieldType'], 'ddMetro') or FieldCore::hasAncestor($this->p['fieldType'], 'ddMetroMultiselect'))) {
      $p['global'] = true;
      $p['table'] = 'tagMetro';
    }
    elseif (!empty($field['settings']['tagsStrName'])) {
      $p['tagsGetterStrName'] = $field['settings']['tagsStrName'];
    }
    return $p;
  }

  function delete() {
    db()->query('DELETE FROM tagGroups WHERE id=?d', $this->p['id']);
    db()->query('DELETE FROM tagItems WHERE strName=? AND groupName=?', $this->p['strName'], $this->p['name']);
    db()->query('DELETE FROM tags WHERE strName=? AND groupName=?', $this->p['strName'], $this->p['name']);
    O::delete(get_class($this), $this->p['name']);
  }

  // ================================= STATIC ================================

  /**
   * Returns Tags Group data
   *
   * @param   string  Structure data
   * @param   string  Tags Group name
   * @return  array
   */
  static function getData($strName, $name, $strict = true) {
    $r = db()->selectRow(<<<SQL
    SELECT
      tagGroups.*,
      dd_fields.title,
      dd_fields.type AS fieldType
    FROM tagGroups
    LEFT JOIN dd_fields ON dd_fields.name=tagGroups.name AND
                           dd_fields.strName=tagGroups.strName
    WHERE
      tagGroups.strName=? AND
      tagGroups.name=?
SQL
      , $strName, $name);
    if ($strict) Misc::checkEmpty("Group $strName/$name does not exists");
    return $r;
  }

  static function getById($id) {
    $r = db()->selectRow('SELECT * FROM tagGroups WHERE id=?d', $id);
    return new DdTagsGroup($r['strName'], $r['name']);
  }

  /**
   * Создает группу
   *
   * @param   string Имя группы
   * @param   integer ID раздела
   * @param   bool Флаг определяющий, управляются ли тэги этой группы тэг-записями
   * @param   bool Флаг определяющий, уникально ли имя тэгов
   * @param   bool Флаг определяющий, могут ли быть тэги древовидными
   */
  static function create($strName, $name, $itemsDirected = true, $unicalTagsName = true, $tree = false) {
    if (!$name) throw new Exception('$name not defined');
    return db()->query('REPLACE INTO tagGroups
       SET strName=?, name=?, itemsDirected=?d, unicalTagsName=?d, tree=?d', $strName, $name, $itemsDirected, $unicalTagsName, $tree);
    //return new TagsGroup($strName, $name);
  }

  /**
   *
   * @todo REFACTOR TO NON-STATIC
   *
   * Переименовывает группы
   *
   * @param   integer Структура группы
   * @param   string Текущее имя группы
   * @param   string Новое имя группы
   * @param   bool Флаг определяющий, управляются ли тэги этой группы тэг-записями
   * @param   bool Флаг определяющий, уникально ли имя тэгов
   * @param   bool Флаг определяющий, могут ли быть тэги древовидными
   */
  static function update($strName, $name, $newName, $itemsDirected = true, $unicalTagsName = true, $tree = false) {
    if (!self::getData($strName, $name)) {
      throw new Exception("Tags group strName=$strName, name=$name does not exists.");
    }
    db()->query('
      UPDATE tagGroups
      SET name=?, itemsDirected=?d, unicalTagsName=?d, tree=?d
      WHERE strName=? AND name=?', $newName, $itemsDirected, $unicalTagsName, $tree, $strName, $name);
    db()->query('UPDATE tags SET groupName=? WHERE groupName=? AND strName=?', $newName, $name, $strName);
    db()->query('UPDATE tagItems SET groupName=? WHERE groupName=? AND strName=?', $newName, $name, $strName);
  }

  /**
   * @return DdItems|false
   */
  function getRelatedItems() {
    if (DdTags::isDdItems($this->p['fieldType'])) {
      $strName = O::get('DdFields', $this->p['strName'], ['getHidden' => true])->getField($this->p['name'])['settings']['strName'];
      return new DdItems($strName);
    }
    return false;
  }

}