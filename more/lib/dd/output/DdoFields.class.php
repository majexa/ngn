<?php

class DdoFields {
  use Options;

  /**
   * Массив с именами полей, разрешенными для вывода
   *
   * @var array
   */
  private $allowedFields;

  /**
   * Имя лейаута полей
   *
   * @var string
   */
  private $layoutName;

  /**
   * Определяет выводит ли текущий класс список записей или одну запись
   *
   * @var string
   */
  public $isItemsList = true;

  /**
   * Флаг определяет существуют ли настройки фильтра для текущего лейаута
   *
   * @var bool
   */
  protected $settingsExists;

  /**
   * Объект полей
   *
   * @var DdFields
   */
  protected $fields;

  /**
   * @var DdoSettings
   */
  protected $settigns;

  /**
   * Дополнительные виртуальные поля, такие как "количество комментариев",
   * "мозаика альбома"  и п.т.
   *
   * @return array
   */
  protected function virtualFields() {
    return [
      'commentsCount' => [
        'name'         => 'commentsCount',
        'oid'          => 200,
        'title'        => 'Количество комментариев',
        'descr'        => 'Ссылка на комментарии с цифрой их количества',
        'type'         => 'commentsCount',
        'extraVirtual' => true
      ],
      'authorId'      => [
        'name'         => 'authorId',
        'oid'          => 400,
        'title'        => 'Автор',
        'type'         => 'user',
        'extraVirtual' => true
      ]
    ];
  }

  protected function defineOptions() {
    return [
      'getAll'  => false,
      'allowed' => []
    ];
  }

  /**
   * @param DdoSettings $ddoSettings Настройки лейаута
   * @param string $layoutName Имя лейаута
   * @param string $strName
   * @param array $options
   */
  function __construct(DdoSettings $ddoSettings, $layoutName, $strName, array $options = []) {
    Misc::checkEmpty($layoutName);
    $this->setOptions($options);
    $allowedFields = $ddoSettings->getAllowedFields($layoutName);
    if (!empty($this->options['getAll']) or $allowedFields) {
      $opt = [
        'getDisallowed' => true,
        'getSystem'     => true,
        'getVirtual'    => true
      ];
    }
    else {
      $opt = [
        'getDisallowed' => false,
        'getSystem'     => false,
        'getVirtual'    => false
      ];
    }
    $this->fields = new DdFields($strName, $opt);
    $this->layoutName = $layoutName;
    $this->settigns = $ddoSettings;
    if ($allowedFields) {
      $this->allowedFields = $allowedFields;
      $this->settingsExists = true;
    }
    else {
      $this->settingsExists = false;
    }
  }

  /**
   * Эти типы не должны выводиться по-умолчанию
   *
   * @var array
   */
  protected $forceListShowTypes = [
    'wisiwig',
    'typoTextarea'
  ];

  protected $forceShowTypes = [
  ];

  function getFields() {
    $fields = $this->fields->getFieldsF();
    $fields += $this->virtualFields();
    $_fields = [];
    foreach ($fields as $k => $v) {
      $allowed = ($this->options['allowed'] and in_array($k, $this->options['allowed']));
      if (!$allowed and empty($this->options['forceAllowed'])) {
        if (!empty($v['notList'])) continue;
        // Если настройки не определены
        if (!$this->settingsExists) {
          // Не выводим системные по умолчанию
          if (!empty($v['extraVirtual'])) continue;
          // Не выводим большие текстовые поля для списков записей
          //if ($this->isItemsList and in_array($v['type'], $this->forceListShowTypes)) continue;
          if (in_array($v['type'], $this->forceShowTypes)) continue;
        }
        if (!$this->allowed($v['name'])) continue;
      }
      $_fields[$k] = $v;
    }
    return $this->order($_fields);
  }

  private function order($fields) {
    if (($order = $this->settigns->getOrder($this->layoutName)) === false) return $fields;
    foreach ($fields as $k => &$v) {
      if (isset($order[$v['name']])) {
        $v['oid'] = $order[$v['name']];
      } elseif ($v['oid'] == 0) {
        // Если порядок переопределён в настройках ddo, 0 считаем не приоритетным
        $v['oid'] = 1000;
      }
    }
    return Arr::sortByOrderKey($fields, 'oid');
  }

  private function allowed($fieldName) {
    if (!$this->allowedFields) return true;
    return in_array($fieldName, $this->allowedFields);
  }

}