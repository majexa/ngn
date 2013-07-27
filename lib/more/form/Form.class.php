<?php

class Form extends FormBase {

  /**
   * @var Fields
   */
  public $oFields;

  public $options = [
    'submitTitle' => 'Сохранить'
  ];

  /**
   * Если флаг включен в форме будут выводится только обязательные поля
   *
   * @var bool
   */
  public $onlyRequired = false;

  public $disableSubmit = false;

  /**
   * @var FormSpamBotBlocker
   */
  public $fsbb;

  public $nospam;

  public $enableFSBB = true;

  //public $enableCaptcha = false;

  public $elementsData = [];

  public $defaultData = [];

  public $disableFormTag = false;

  public $create = false;

  static $counter = 1;

  /**
   * @param array/Fields $fields
   * @param array $options
   */
  function __construct($fields, array $options = []) {
    if (is_array($fields)) $fields = new Fields($fields);
    elseif (!is_a($fields, 'Fields')) throw new Exception("\$fields is not Fields type");
    $this->oFields = $fields;
    self::$counter++;
    parent::__construct($options);
    $this->init();
    if (!$this->isSubmitted() and !empty($this->options['defaultsFromReq'])) $this->defaultData = $this->req->r;
  }

  /**
   * @return string Уникальный идентификатор формы
   */
  function id() {
    return 'f'.md5(serialize($this->oFields->fields));
  }

  protected function init() {
  }

  private function initFSBB() {
    // Init FormSpamBotBlocker
    if ($this->enableFSBB) {
      if ($this->fsbb) return;
      $this->fsbb = new FormSpamBotBlocker;
      $this->fsbb->hasSession = false;
      $this->nospam = false; // Если FSBB включен, определяем включаем флаг отсутствия спама
    }
    else {
      $this->nospam = true;
    }
  }

  // Action Field
  protected $defaultActionName = 'action';

  protected $hiddenFieldsData = [];

  protected $actionFieldValue;

  function setActionFieldValue($v) {
    $this->actionFieldValue = $v;
  }

  function addHiddenField($data) {
    if (!isset($data['name'])) throw new Exception('Name not defined in: '.getPrr($data));
    $data['type'] = 'hidden';
    $this->hiddenFieldsData[] = $data;
  }

  protected $defaultElementsDefined = false;

  protected function initDefaultElements() {
    if ($this->defaultElementsDefined) return;
    if ($this->disableFormTag) return;
    $this->addHiddenField([
      'name'    => 'formId',
      'value'   => $this->id(),
      'noValue' => true
    ]);
    if (!empty($this->actionFieldValue)) {
      $this->addHiddenField([
        'name'    => $this->defaultActionName,
        'value'   => $this->actionFieldValue,
        'noValue' => true
      ]);
    }
    if (isset($_SERVER['HTTP_REFERER'])) {
      $this->addHiddenField([
        'name'    => 'referer',
        'value'   => $_SERVER['HTTP_REFERER'],
        'noValue' => true
      ]);
    }
    foreach ($this->hiddenFieldsData as $v) {
      $this->createElement($v);
    }
    $this->defaultElementsDefined = true;
    if (!empty($this->defaultElements)) foreach ($this->defaultElements as $v) {
      $this->createElement($v);
    }
  }

  public $defaultElements;

  protected $noDataTypes = [
    'html'
  ];

  /**
   * Определяет откуда пришли данные для формы, из HTTP запроса или напрямую через массив
   * Это необходимо, что бы в файловых полях не использовать массив _FILES в случае без запроса
   *
   * @var bool
   */
  public $fromRequest = true;

  protected $elementsInitialized = false;

  protected function initElements($reset = false) {
    if ($reset) $this->els = [];
    if ($this->elementsInitialized and !$reset) return;
    $this->elementsInitialized = true;
    $this->hasErrors = false;
    if ($this->onlyRequired) {
      $fields = $this->oFields->getRequired();
    }
    else {
      // Здесь необходимо использовать getFieldsF, потому что она возвращает только видимые поля,
      // без системных и скрытых. А по идее там осуществляются вские ненужные операции.. кажется
      // + ещё права там проверяются
      $fields = $this->oFields->getFieldsF();
    }
    foreach ($fields as $v) {
      if ($this->oFields->isFileType($v['name'])) {
        $value = BracketName::getValue($this->defaultData, $v['name'], BracketName::modeString);
      }
      else {
        $value = BracketName::getValue($this->elementsData, $v['name'], BracketName::modeNull);
      }
      if (!empty($v['default'])) $v['value'] = $v['default'];
      if ($value !== null) $v['value'] = $value;
      BracketName::setValue($this->elementsData, $v['name'], $this->createElement($v)->value());
    }
    if (!$this->disableSubmit) {
      $this->createElement([
        'value' => $this->options['submitTitle'],
        'type'  => 'submit'
      ]);
    }
  }

  /**
   * Генерирует поля и возвращает их значения
   *
   * @param   array   Значения по умолчанию
   * @return  array
   */
  function setElementsData(array $defaultData = [], $reset = true) {
    // pr($defaultData);
    $this->defaultData = $defaultData;
    $this->elementsData = $defaultData;
    if ($this->isSubmitted() and $this->fromRequest) $this->elementsData = $this->req->p;
    $this->initElements($reset);
    return $this;
  }

  protected $elementsDefaultDefined = false;

  /**
   * Функция вызывается при рендеренге формы, если поля не были
   * определены ф-ей setFieldsData()
   */
  protected function setElementsDataDefault() {
    if ($this->elementsDefaultDefined) return false;
    $this->setElementsData($this->defaultData);
    $this->elementsDefaultDefined = true;
    return true;
  }

  function getElements() {
    $this->setElementsDataDefault();
    return parent::getElements();
  }

  function getElement($name) {
    $this->setElementsDataDefault();
    return parent::getElement($name);
  }

  /**
   * Выводит только указанные для инициализации поля
   *
   * @param   bool  Флаг
   */
  function outputOnlyFields($flag = true) {
    $this->disableFormTag = $flag;
    $this->disableSubmit = $flag;
    $this->disableJs = $flag;
  }

  function getFields() {
    return $this->oFields->getFieldsF();
  }

  function fsbb() {
    if ($this->hasErrors) return;
    $this->initFSBB();
    // Проверяем на спам, если есть сабмит и добавляем ошибку, если проверку не прошла
    if ($this->enableFSBB and $this->isSubmitted()) {
      // Ах да.. только в том случае, если засабмичено
      //if ($this->defaultData) throw new Exception('default data not exists');
      $this->nospam = $this->fsbb->checkTags($this->defaultData);
      if (!$this->nospam) {
        $this->globalError('Не прошла проверка на спам. <a href="'.Tt()->getPath().'">Попробуйте заполнить форму ещё раз</a>');
      }
    }
  }

  function html() {
    $this->setElementsDataDefault();
    $this->initDefaultElements();
    if ($this->disableSubmit) {
      foreach ($this->els as $k => $el) if ($el->type == 'submit') unset($this->els[$k]);
    }
    $html = parent::html();
    $html = str_replace('</form>', $this->htmlVisibilityConditions().'</form>', $html);
    if (isset($this->fsbb)) {
      $html = str_replace('</form>', $this->fsbb->makeTags().'</form>', $html);
    }
    //$html = str_replace('method="post">', 'method="post"><div class="time">'.date('H:i:s').'</div>', $html);
    return $html;
  }

  // ====================== Visibility Conditions ================

  protected $visibilityConditions = [];

  /**
   * Добавляет условие видимости определенных секций.
   * Секцией называется html-элемент вида <div class="hgrp hgrp_headerName">,
   * с которого начинается заголовочное поле, открывающее секцию.
   *
   * Добавляемые условия используются в javascript'е для динамического
   * отображения и скрытия секций.
   *
   * @param   string  Имя заголовочного поля, открывающее секцию или обычного поля
   * @param   string  Имя поля, от которого зависит отображать ли секцию
   * @param   string  Условие отображения в формате "$v == 4",
   *                  где $v - текущее значение поля $condFieldName
   * @param   string  header/field
   *
   */
  function addVisibilityCondition($sctionName, $condFieldName, $cond, $type = 'header') {
    $this->visibilityConditions[] = [
      'headerName'    => $sctionName,
      'condFieldName' => $condFieldName,
      'cond'          => $cond,
      'type'          => $type
    ];
  }

  protected $dependRequire = [];

  function addDependRequire($dependName, $requireName) {
    $this->dependRequire[] = [$dependName, $requireName];
  }

  /*
  protected function jsVisibilityConditions() {
    if (empty($this->visibilityConditions)) return '';
    foreach ($this->visibilityConditions as $v)
      $s .= "Ngn.frm.visibilityCondition(
eForm, '{$v['headerName']}', '{$v['condFieldName']}', '{$v['cond']}');";
    return $s;
  }
  */

  protected function htmlVisibilityConditions() {
    if (empty($this->visibilityConditions)) return '';
    foreach ($this->visibilityConditions as $v) $r[] = array_values($v);
    return '<div class="visibilityConditions" style="display:none">'.json_encode($r).'</div>';
  }

  function isSubmitted() {
    if (!$this->fromRequest) return true;
    return (isset($this->req->p['formId']) and $this->req->p['formId'] == $this->id());
  }

  function isSubmittedAndValid() {
    $this->setElementsDataDefault();
    if (!parent::isSubmittedAndValid()) return false;
    return true;
  }

  // Функционал для апдейта данных через класс формы

  function update() {
    if (!$this->isSubmittedAndValid()) return false;
    $this->_update($this->getData());
    return true;
  }

  protected function _update(array $data) {
  }

  function debugElements() {
    $this->setElementsDataDefault();
    die2(Arr::get($this->els, 'options'));
  }

  public $tinyInitialized = false;

  protected function jsInlineUpload() {
    $opt = empty($this->options['uploadOptions']) ? '' : Arr::jsObj($this->options['uploadOptions']);
    return "
(function() {
  Ngn.Form.forms.{$this->id()}.initUpload($opt);
}).delay(100);
";
  }

  /**
   * Добавляет информацию об кол-ве оставшихся знаков для ввода
   */
  protected function jsMaxLength() {
    return "
Ngn.frm.maxLength($('{$this->id()}'), ".FieldEInput::defaultMaxLength.");
";
  }

  function getTitledData() {
    $r = [];
    foreach ($this->getElements() as $name => $el) {
      if (!empty($el['noValue'])) continue;
      if (!empty($this->options['filterEmpties']) and $el->isEmpty()) continue;
      $r[$name] = [
        'title' => $this->oFields->fields[$name]['title'],
        'value' => $el->titledValue()
      ];
    }
    return $r;
  }

  function hasAttachebleWisiwig() {
    return Arr::getValueByKey($this->oFields->fields, 'type', 'wisiwig') !== false;
  }

}
