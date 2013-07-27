<?php

class DdFieldCore {

  static function isGroup($type) {
    return FieldCore::hasAncestor($type, 'headerAbstract');
  }

  static function getIconPath($type) {
    return file_exists(NGN_PATH.'/i/img/icons/fields/'.$type.'.gif') ? './i/img/icons/fields/'.$type.'.gif' : './i/img/blank.gif';
  }

  static function getFieldsFromTable($strName) {
    return Arr::get(db()->select("SHOW COLUMNS FROM dd_i_$strName"), 'Fields');
  }

  static function isNumberType($type) {
    return FieldCore::hasAncestor($type, 'num') or FieldCore::hasAncestor($type, 'float');
  }

  static function isBoolType($type) {
    return FieldCore::hasAncestor($type, 'bool') or FieldCore::hasAncestor($type, 'boolCheckbox');
  }

  static function isRangeType($type) {
    return in_array($type, ['numberRange']);
  }

  static function isFormatType($type) {
    return in_array($type, ['textarea']);
  }

  /**
   * Регистрирует dd-поле
   *
   * @param string Тип поля. Должен быть равен имени класса элемента поля этого типа, с обрезанным префиксом
   * @param array  virtual - означает, что поле не создает данных и в таблице для него будет определена колонка с типом и длинной по умолчанию
   *               notList - не выводить значение поля
   *               system  - у поля нет редактируемого элемента
   *               noElementTag - при выводе нет обрамляющего тэга .element
   *
   */
  static function registerType($type, array $data) {
    Arr::checkEmpty($data, ['title', 'order']);
    if (!empty($data['virtual'])) $data = array_merge($data, [
      'dbType'   => 'INT',
      'dbLength' => 1
    ]);
    Arr::checkEmpty($data, 'dbType');
    if (!preg_match('/(.*TEXT|DATE|TIME|DATETIME)/', $data['dbType'])) Arr::checkEmpty($data, 'dbLength');
    self::$types[$type] = $data;
  }

  static protected $types = [];

  static function getTypeData($type, $strict = true) {
    if (($r = ProjMem::get("ddFieldTypeData_$type")) !== false) return $r;
    if (Lib::exists(FieldCore::getClass($type))) Lib::required(FieldCore::getClass($type));
    if (!isset(self::$types[$type])) {
      if ($strict) throw new EmptyException("There is no such registered ddType as '$type'");
      else
        return false;
    }
    $r = self::$types[$type];
    $r['type'] = $type;
    if (empty($r['dbLength'])) $r['dbLength'] = null;
    ProjMem::set("ddFieldTypeData_$type", $r);
    return $r;
  }

  static function typeExists($type) {
    if (Lib::exists(FieldCore::getClass($type))) Lib::required(FieldCore::getClass($type));
    return isset(self::$types[$type]);
  }

  /**
   * Возвращает данные типов динамических полей
   * @return array
   */
  static function getTypes() {
    foreach (ClassCore::getClassesByPrefix('FieldE') as $class) // Регистрация типа dd-поля происходит в классе элмента
      Lib::required($class);
    return Arr::sortByOrderKey(self::$types, 'order');
  }

}

$r = [
  'col'           => [
    'title'   => 'Колонка',
    'virtual' => true,
    'notList' => true,
    'order'   => 15
  ],
  'text'          => [
    'dbType'   => 'VARCHAR',
    'dbLength' => 255,
    'title'    => 'Одностройчное поле',
    'order'    => 20
  ],
  'typoText'      => [
    'dbType'   => 'VARCHAR',
    'dbLength' => 255,
    'title'    => 'Одностройчное поле (с типографированием)',
    'order'    => 10
  ],
  'boolCheckbox'  => [
    'dbType'   => 'int',
    'dbLength' => 1,
    'title'    => 'Да / нет (чекбокс)',
    'order'    => 20
  ],
  'bool'          => [
    'dbType'   => 'int',
    'dbLength' => 1,
    'title'    => 'Да / нет (радио)',
    'order'    => 30
  ],
  'file'          => [
    'dbType'   => 'VARCHAR',
    'dbLength' => 255,
    'title'    => 'Файл',
    'order'    => 40
  ],
  'imagePreview'  => [
    'dbType'   => 'VARCHAR',
    'dbLength' => 255,
    'title'    => 'Изображение',
    'order'    => 50
  ],
  'email'         => [
    'dbType'   => 'VARCHAR',
    'dbLength' => 255,
    'title'    => 'E-mail',
    'order'    => 60
  ],
  'emails'         => [
    'dbType'   => 'TEXT',
    'title'    => 'E-mail`ы',
    'order'    => 60
  ],
  'date'          => [
    'dbType' => 'DATE',
    'title'  => 'Дата',
    'order'  => 70
  ],
  'time'          => [
    'dbType' => 'TIME',
    'title'  => 'Время',
    'order'  => 80
  ],
  'datetime'      => [
    'dbType' => 'DATETIME',
    'title'  => 'Дата, время',
    'order'  => 90
  ],
  'typoTextarea'  => [
    'dbType' => 'TEXT',
    'title'  => 'Многострочное поле',
    'order'  => 100
  ],
  'wisiwig'       => [
    'dbType' => 'TEXT',
    'title'  => 'Текстовое поле с визуальным редактором (с поддержкой вложенных изображений, файлов, таблиц и пр.)',
    'order'  => 110
  ],
  'wisiwigSimple' => [
    'dbType' => 'TEXT',
    'title'  => 'Текстовое поле с базовым визуальным редактором',
    'order'  => 111
  ],
  'num'           => [
    'dbType'   => 'INT',
    'dbLength' => 11,
    'title'    => 'Целое число',
    'order'    => 120
  ],
  'name'           => [
    'dbType'   => 'VARCHAR',
    'dbLength' => 255,
    'title'    => 'Имя',
    'order'    => 120
  ],
  'domain'           => [
    'dbType'   => 'VARCHAR',
    'dbLength' => 255,
    'title'    => 'Домен',
    'order'    => 120
  ],
  'float'         => [
    'dbType'   => 'float',
    'dbLength' => 11,
    'title'    => 'Дробное число',
    'order'    => 130
  ],
  'price'         => [
    'dbType'   => 'FLOAT',
    'dbLength' => 11,
    'title'    => 'Деньги',
    'order'    => 140
  ],
  'procent'       => [
    'dbType'   => 'INT',
    'dbLength' => 11,
    'title'    => 'Проценты',
    'order'    => 140
  ],
  'static'        => [
    'dbType'   => 'VARCHAR',
    'dbLength' => 255,
    'title'    => 'Статический текст',
    'virtual'  => true,
    'order'    => 150
  ],
  'ddStaticText'  => [
    'dbType'   => 'VARCHAR',
    'dbLength' => 255,
    'title'    => 'Статический текст в форме',
    'virtual'  => true,
    'order'    => 150
  ],
  'floatBlock'    => [
    'title'        => 'Блок для обтекания',
    'order'        => 160,
    'virtual'      => true,
    'system'       => true,
    'noElementTag' => true
  ],
  'header'        => [
    'title'   => 'Заголовок',
    'order'   => 160,
    'virtual' => true,
    //'system' => true,
  ],
  'headerClose'        => [
    'title'   => 'Заголовок конец',
    'order'   => 160,
    'virtual' => true,
    //'system' => true,
  ],
  'headerToggle'  => [
    'title'   => 'Заголовок-переключатель',
    'order'   => 160,
    'virtual' => true,
    //'system' => true,
  ],
  'groupBlock'    => [
    'title'        => 'Блок для группировки',
    'order'        => 160,
    'virtual'      => true,
    'system'       => true,
    'noElementTag' => true
  ],
  'url'           => [
    'dbType' => 'TEXT',
    'title'  => 'Одна ссылка',
    'order'  => 170
  ],
  'urls'          => [
    'dbType' => 'TEXT',
    'title'  => 'Несколько ссылок',
    'order'  => 180
  ],
  'icq'           => [
    'dbType'   => 'INT',
    'dbLength' => 15,
    'title'    => 'ICQ#',
    'order'    => 190
  ],
  'skype'         => [
    'dbType'   => 'VARCHAR',
    'dbLength' => 255,
    'title'    => 'Skype',
    'order'    => 200
  ],
  'phone'         => [
    'dbType'   => 'VARCHAR',
    'dbLength' => 255,
    'title'    => 'Телефон',
    'order'    => 210
  ],
  'sound'         => [
    'dbType'   => 'VARCHAR',
    'dbLength' => 255,
    'title'    => 'Аудио',
    'order'    => 220
  ],
  'video'         => [
    'dbType'   => 'VARCHAR',
    'dbLength' => 255,
    'title'    => 'Видео',
    'order'    => 220
  ],
  'user'          => [
    'dbType'   => 'INT',
    'dbLength' => 11,
    'title'    => 'Пользователь',
    'order'    => 230
  ],
  'birthDate'     => [
    'dbType' => 'DATE',
    'title'  => 'Дата рождения',
    'order'  => 90
  ],
  'fullName'      => [
    'dbType'   => 'VARCHAR',
    'title'    => 'Ф.И.О.',
    'dbLength' => 255,
    'order'    => 90
  ],
  'configSelect'  => [
    'dbType'   => 'VARCHAR',
    'title'    => 'Список из конфигурации',
    'dbLength' => 255,
    'order'    => 90
  ],
  'numberRange'   => [
    'dbType'            => 'INT',
    'dbLength'          => 15,
    'title'             => 'Диапозон чисел',
    'order'             => 90000,
    'disableTypeChange' => true
  ],
];

foreach ($r as $type => $data) DdFieldCore::registerType($type, $data);
