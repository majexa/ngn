<?php

class DdStructuresManager extends DbItemsManager {

  protected function afterFormElementsInit() {
    // убираем из списка текущую редактируемую структуру
    if (($el = $this->form->getElement('filterStrName')) !== false) unset($el->options['options'][$this->defaultData['name']]);
  }

  function __construct() {
    $fields = [
      [
        'type' => 'col'
      ],
      [
        'name'     => 'title',
        'title'    => 'Название структуры',
        'required' => true
      ],
      [
        'name'     => 'name',
        'title'    => 'Имя',
        'type'     => 'ddStructureName',
        'required' => true
      ]
    ];
    if (Config::getVarVar('dd', 'enableFilters')) {
      $fields[] = [
        'name'        => 'filterStrName',
        'title'       => 'Структура фильтра',
        'type'        => 'ddStructure',
        'allowedType' => 'dynamic'
      ];
    }
    $fields = Arr::append($fields, [
      [
        'type' => 'col'
      ],
      [
        'name'    => 'type',
        'title'   => 'Тип структуры',
        'type'    => 'select',
        'options' => DdStructureCore::getTypes(),
        'help'    => 'Статические структуры используются для разделов, предполагающих только одну единственную запись. Например простой текстовый раздел, где страница - это одна запись.'
      ],
      [
        'name'  => 'locked',
        'title' => 'структура с ограниченным доступом',
        'type'  => 'bool',
        'value' => false,
        'help'  => 'Для структур с ограниченным доступом в папку с файлами "u/strName" добавляется файл ".htaccess", запрещающий доступ к файлам.
Все файлы из этой папки получаются только через метод "action_getLockFile"'
      ],
      [
        'name'  => 'indx',
        'title' => 'разрешить индексацию структуры',
        'type'  => 'bool',
        'help'  => 'Такие структуры, как, например, "Баннеры" не нуждаются в индексации, т.к. поиск по ним не нужен'
      ],
      [
        'type' => 'col'
      ],
      [
        'name'  => 'descr',
        'title' => 'Описание',
        'type'  => 'textarea',
      ]
    ]);
    parent::__construct(new DdStructureItems, new Form($fields));
  }

  protected function beforeCreate() {
    $this->createTable($this->data['name']);
    $this->tableComment($this->data['name'], $this->data['title']);
  }

  protected function afterCreateUpdate() {
    if (isset($this->data['type']) and ($this->data['type'] == 'static' or $this->data['type'] == 'variant')) {
      (new DdFieldsManager($this->data['name']))->create([
        'name'     => 'static_id',
        'title'    => 'static_id',
        'type'     => 'num',
        'system'   => 1,
        'editable' => 0,
        'virtual'  => 1,
        'notList'  => 1
      ]);
    }
  }

  protected function tableComment($name, $title) {
    db()->query("ALTER TABLE ".DdCore::table($name)." COMMENT=?", $title);
  }

  protected function createTable($name) {
    $table = DdCore::table($name);
    db()->query("
    CREATE TABLE $table (
      id INT(11) NOT NULL,
      oid INT(11) NOT NULL,
      pageId INT(11) NULL,
      active INT(1) NOT NULL DEFAULT 1,
      dateCreate DATETIME NULL,
      dateUpdate DATETIME NULL,
      ip VARCHAR(15) NULL,
      userId INT(11) NULL,
      userGroupId INT(11) NULL,
      clicks INT(11) NOT NULL DEFAULT '0'
    ) ENGINE=InnoDB");
    db()->query("ALTER TABLE $table DEFAULT CHARACTER SET ".db()->charset." COLLATE ".db()->collate);
    db()->query("ALTER TABLE $table ADD UNIQUE (id)");
    db()->query("ALTER TABLE $table CHANGE id id INT(11) AUTO_INCREMENT");
    db()->query("ALTER TABLE $table ADD INDEX (pageId)");
  }

  /**
   * Создает поля по умолчанию необходимые для данного типа структуры
   *
   * @param   string  Имя структуры
   * @param   string  Тип структуры
   */
  protected function createDefaultFields($strName, $type) {
    $o = new DdFieldsManager($strName);
    foreach (DdStructureCore::getDefaultFields($type) as $v) {
      $o->create($v);
    }
  }

  // ----------------------- Update ---------------------------

  protected function beforeUpdate() {
    $old = $this->beforeUpdateData['name'];
    $new = $this->data['name'];
    // Изменяем папку файлов
    if ($old != $new) {
      $this->renameFilesDb($old, $new);
      $this->renameFilesFolder($old, $new);
      $this->renameTable($old, $new);
      // Перемещаем поля в структуру с новым именем
      db()->query("UPDATE dd_fields SET strName=? WHERE strName=?", $new, $old);
      // Переименовываем папку с файлами
      $oldDir = UPLOAD_PATH.'/'.DdCore::filesDir($old);
      if (file_exists($oldDir)) rename($oldDir, UPLOAD_PATH.'/'.DdCore::filesDir($new));
      // Меняем имя структуры в настройках разделов
      //DbModelPages::updateStrName($old, $new);
      // Структуры в тэгах
      db()->query('UPDATE tags SET strName=? WHERE strName=?', $new, $old);
      db()->query('UPDATE tagGroups SET strName=? WHERE strName=?', $new, $old);
      db()->query('UPDATE tagItems SET strName=? WHERE strName=?', $new, $old);
      db()->query("UPDATE dd_structures SET name=?, title=?, descr=?, type=? WHERE name=?", $new, $this->data['title'], $this->data['descr'], $this->data['type'], $old);
    }
    $this->tableComment($new, $this->data['title']);
  }

  protected function renameTable($oldName, $newName) {
    db()->query("RENAME TABLE ".DdCore::table($oldName)." TO ".DdCore::table($newName));
  }

  protected function renameFilesDb($oldName, $newName) {
    $oF = new DdFields($oldName);
    $fieldNames = array_keys($oF->getFileFields());
    if (empty($fieldNames)) return;
    foreach (db()->query("SELECT id, ".implode(', ', $fieldNames)." FROM ".DdCore::table($oldName)) as $v) {
      foreach ($fieldNames as $fieldName) $v[$fieldName] = str_replace('/'.$oldName.'/', '/'.$newName.'/', $v[$fieldName]);
      $id = $v['id'];
      unset($v['id']);
      db()->query("UPDATE ".DdCore::table($oldName)." SET ?a WHERE id=?d", $v, $id);
    }
  }

  protected function renameFilesFolder($oldName, $newName) {
    if (file_exists(UPLOAD_PATH.'/dd/'.$oldName)) rename(UPLOAD_PATH.'/dd/'.$oldName, UPLOAD_PATH.'/dd/'.$newName);
  }

  protected function beforeDelete() {
    //(new DdoSettings($this->data['name']))->delete();
    (new DdFieldsManager($this->data['name']))->deleteFields(); // Удаление полей
    $this->deleteTable($this->data['name']);
    Dir::remove(UPLOAD_PATH.'/'.DdCore::filesDir($this->data['name']));
  }

  protected function deleteTable($name) {
    db()->delete(DdCore::table($name));
  }

  function deleteByName($name) {
    if (!($id = db()->selectCell("SELECT id FROM dd_structures WHERE name=?", $name))) return;
    O::delete('DdFields', $name);
    $this->delete($id);
  }

}