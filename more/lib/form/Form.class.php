  <?php

class Form {
  use Options, CallOnce;

  public $templates = [
    'form'        => '{input}<div class="clear"></div>',
    'headerOpen'  => '<div class="clearfix hgrp hgrp_{name}{class}">',
    'headerClose' => '</div>',
    'input'       => '<div class="element{rowClass}">{title}<div class="field-wrapper">{input}</div>{error}{help}</div>',
    'title'       => '<p class="label"><span class="ttl">{title}</span>{required}<span>:</span></p>',
    'error'       => '<div class="advice-wrapper static-advice" style="z-index:300"><div class="corner"></div><div class="validation-advice">{error}</div></div>',
    'globalError' => '<div class="element errorRow padBottom"><div class="validation-advice">{error}</div></div>',
    'help'        => '<div class="clear"><!-- --></div><div class="help"><small>{help}</small></div>',
    'required'    => '<b class="reqStar" title="Обязательно для заполнения" style="cursor:help">*</b>',
    'element'     => '' // используется в ф-ии html(), если флаг $this->isDdddTpl = true
  ];

  /**
   * array containing all the web form elements.
   *
   * @var array
   */
  public $els;

  /**
   * Encryption type of the form. Switches to "multipart/form-data" when using
   * a file upload element.
   *
   * @var string
   */
  public $encType = '';

  /**
   * @var bool
   */
  public $htmlentities = true;

  /**
   * This will be true if throw new Exception() was called at least once
   *
   * @var bool
   */
  public $hasErrors = false;

  /**
   * Определяет наличие обрамляющего группу полей тэга
   *
   * @var bool
   */
  public $isHeaderGroupTags = true;

  /**
   * Выключить отображение тега <form>
   *
   * @var bool
   */
  public $disableFormTag = false;

  /**
   * Использовать dddd-шаблоны в $this->templates
   *
   * @var bool
   */
  public $isDdddTpl = false;

  /**
   * Action value for FORM tag
   *
   * @var string
   */
  public $action = null;

  /**
   * Типы элементов, перед которыми заголовочные элементы будут закрываться
   *
   * @var array
   */
  protected $closingHeaderTypes = ['submit'];

  /**
   * @var Req
   */
  public $req;

  /**
   * @var Fields
   */
  public $fields;

  /**
   * Если флаг включен в форме будут выводится только обязательные поля
   *
   * @var bool
   */
  public $onlyRequired = false;

  public $disableSubmit = false;

  /**
   * @var FormSpamBlocker
   */
  public $fsb;

  public $nospam;

  public $enableFsb = true;

  public $elementsData = [];

  public $defaultData = [];

  public $create = false;

  static $counter = 1;

  /**
   * @param array /Fields
   * @param array $options
   */
  function __construct($fields, array $options = []) {
    if (is_array($fields)) $fields = new Fields($fields);
    if (!is_a($fields, 'Fields')) throw new Exception("\$fields is not Fields type (".get_class($fields).")");
    $this->fields = $fields;
    self::$counter++;
    $this->setOptions($options);
    if ($this->options['placeholders']) {
      Sflm::frontend('js')->addObject('Ngn.PlaceholderSupport');
      $this->templates['input'] = str_replace('{title}', '', $this->templates['input']);
      $this->templates['input'] = str_replace('{input}', '{required}{input}', $this->templates['input']);
      $this->templates['title'] = '';
    }
    $this->req = empty($this->options['req']) ? O::get('Req') : $this->options['req'];
    $this->init();
  }

  protected function getDefaultData() {
    if ($this->isSubmitted() and !empty($this->options['defaultsFromReq'])) {
      return $this->req->r;
    }
    return $this->defaultData;
  }

  function isSubmitted() {
    if (!$this->fromRequest) return true;
    return $this->req['formId'] and $this->req['formId'] == $this->id();
  }

  protected function elementExists($name) {
    return isset($this->els[$name]);
  }

  /**
   * @param  string Имя поля
   *
   * @return FieldEAbstract
   */
  function getElement($name) {
    $this->setElementsDataDefault();
    if (!isset($this->els[$name])) return false;
    return $this->els[$name];
  }

  function getElements() {
    $this->setElementsDataDefault();
    return $this->els;
  }

  protected $globalError;

  function globalError($message) {
    $this->lastError = $this->globalError = 'Ошибка: '.$message;
    $this->hasErrors = true;
  }

  function isSubmittedAndValid() {
    $this->setElementsDataDefault();
    if (!$this->isSubmitted() or !$this->validate()) return false;
    return true;
  }

  protected function tagParams() {
    if ($this->options['placeholders']) return Html::params(['class' => 'placeholders']);
    return '';
  }

  /*
  protected function dataParams() {
    $class = get_class($this);
    if ($class != 'Form') {
      Sflm::frontend('js')->addObject('Ngn.Form.'.$class);
      return ['class' => $class];
    }
    return false;
  }
  */

  protected function dataParams() {
    return false;
  }

  public $methodPost = true;

  protected function htmlFormOpen() {
    if (!$this->disableFormTag) {
      $html = '<form action="'.($this->action ? $this->action : $this->req->options['uri']).'"';
      $html .= $this->tagParams();
      if (($data = $this->dataParams())) $html .= Html::dataParams($data);
      if (!empty($this->encType)) $html .= ' enctype="'.$this->encType.'"';
      if (!empty($this->options['name'])) $html .= ' name="'.$this->options['name'].'"';
      $html .= ' id="'.$this->id().'" method="'.($this->methodPost ? 'post' : 'get').'">';
      return $html;
    }
    else {
      return '';
    }
  }

  protected function htmlElementInput(FieldEAbstract $el, $input) {
    $input = str_replace('{input}', $input, $this->templates['input']);
    $input = str_replace('{required}', $el['required'] ? $this->templates['required'] : '', $input);
    $input = str_replace('{name}', $el['name'], $input);
    $input = str_replace('{value}', is_array($el['value']) ? '' : $el['value'], $input);
    $input = str_replace('{id}', $el['id'], $input);
    $input = str_replace('{rowClass}', $this->htmlGetRowClassAtr($el), $input);
    return $input;
  }

  protected function htmlElementTitle(FieldEAbstract $el, $elHtml) {
    // Добавляем к лейблу поля знак 'required' если таковой имеется в шаблонах
    if (!empty($el['noTitle']) or empty($el['title'])) {
      $elHtml = str_replace('{required}', $this->templates['required'], $elHtml);
      return str_replace('{title}', '', $elHtml);
    }
    // Если нет шаблона для вывода заголовка
    if (empty($this->templates['title'])) return str_replace('{title}', $el['title'], $elHtml);
    $templateLabel = str_replace('{required}', !empty($el['required']) ? $this->templates['required'] : '', $this->templates['title']);
    $elHtml = str_replace('{title}', $templateLabel, $elHtml);
    return str_replace('{title}', $el['title'], $elHtml);
  }

  protected function htmlElementError($el, $elHtml) {
    if (!empty($el->error)) {
      if (!strstr($elHtml, '{error}')) {
        // Если в шаблоне 'input' нет места для ошибки, заменяем ею лейбел
        $elHtml = str_replace("{label}", $el->error, $elHtml);
      }
      else {
        // Иначе заменяем строку "{error}" на ошибку
        $elHtml = str_replace('{error}', $this->templates['error'], $elHtml);
        $elHtml = str_replace('{error}', $el->error, $elHtml);
      }
    }
    else {
      $elHtml = str_replace('{error}', '', $elHtml);
    }
    return $elHtml;
  }

  protected function htmlElementHelp(FieldEAbstract $el, $elHtml) {
    $elHtml = str_replace('{help}', $this->templates['help'], $elHtml);
    if (!empty($el['help'])) {
      $help = str_replace("\n", "<br />", $el['help']);
    }
    else {
      $help = '';
    }
    return str_replace('{help}', $help, $elHtml);
  }

  protected function htmlGetRowClassAtr(FieldEAbstract $el) {
    $rowClassAtr = (empty($el['id']) ? '' : ' row_'.$el['id']).' type_'.$el->type.(empty($el->options['name']) ? '' : ' name_'.$el->options['name']);
    if (!empty($el->error)) $rowClassAtr .= ' errorRow';
    if (!empty($el['rowClass'])) $rowClassAtr .= ' '.$el['rowClass'];
    return $rowClassAtr;
  }

  protected function htmlGetDefaultAtr($row) {
    return ' name="'.$row['name'].'" id="'.Misc::name2id($row['name']).'i"';
  }

  protected function htmlElement(FieldEAbstract $el) {
    if (is_a($el, 'FieldEHeaderAbstract')) {
      // Для хедеров всё совсем иначе
      return $this->htmlHeader($el);
    }
    if (is_a($el, 'FieldEEmpty')) {
      return $this->htmlHeaderGroupClose($el['depth']);
    }
    $input = $el->html();
    if (!empty($el['noRowHtml'])) {
      // Для этих типов будет выводиться чисто <INPUT>
      return $input;
    }
    if ($this->isDdddTpl) {
      $elHtml = St::dddd($this->templates['element'], array_merge($el, ['input' => $input]));
    }
    else {
      $elHtml = $this->htmlElementInput($el, $input);
      $elHtml = $this->htmlElementError($el, $elHtml);
      $elHtml = $this->htmlElementTitle($el, $elHtml);
      $elHtml = $this->htmlElementHelp($el, $elHtml);
    }
    if (in_array($el->type, $this->closingHeaderTypes)) $elHtml = $this->closeAllOpenedHeaders('closing type').$elHtml;
    return $elHtml;
  }

  protected $curHeaderId;
  protected $headerOpened = [];

  protected function htmlHeaderGroupClose($elementDepth, $comments = '') {
    // Закрываем контейнер группы
    if (!$this->headerOpened($elementDepth)) return ''; // throw new Exception("Header depth={{$elementDepth}} alreay closed. ($comments).");
    if (!$this->isHeaderGroupTags) return '';
    $this->setHeaderOpened($elementDepth, false);
    return $this->templates['headerClose']."<!-- Close fields group depth={{$elementDepth}} ($comments) -->";
  }

  protected function setHeaderOpened($elementDepth, $flag) {
    $this->headerOpened[$elementDepth] = $flag;
  }

  protected $visibleRowN;

  protected $js = '';
  protected $jsInline = '';
  protected $jsInlineDynamic = '';

  /**
   * Возвращает HTML формы
   *
   * @return string
   */
  function html() {
    $this->setElementsDataDefault();
    if ($this->disableSubmit) {
      foreach ($this->els as $k => $el) if ($el->type == 'submit') unset($this->els[$k]);
    }
    $html = $this->htmlFormOpen();
    if ($this->globalError) $html .= str_replace('{error}', $this->globalError, $this->templates['globalError']);
    $this->visibleRowN = -1;
    $elsHtml = '';
    foreach ($this->els as $el) {
      if ($el['type'] == 'hidden') continue;
      $this->visibleRowN++;
      $elsHtml .= $this->htmlElement($el);
    }
    $elsHtml .= $this->closeAllOpenedHeaders('end of elements');
    $elsHtml = $this->wrapCols($elsHtml);
    // Если были колонки, нужно их очистить
    foreach ($this->els as $el) {
      if ($el['type'] != 'hidden') continue;
      $elsHtml .= $this->htmlElement($el);
    }
    $html = $html.str_replace('{input}', $elsHtml, $this->templates['form']);
    $html .= $this->htmlVisibilityConditions();
    if (isset($this->fsb)) $html .= $this->fsb->makeTags();
    if (!$this->disableFormTag) $html .= '</form>';
    return $html.$this->js();
  }

  protected function initElementsInlineJs() {
    $jsTypesAdded = [];
    $elsHtml = '';
    foreach ($this->els as $el) {
      if ($el['type'] == 'hidden') continue;
      $this->visibleRowN++;
      $elsHtml .= $this->htmlElement($el);
      if (($js = $el->js()) == '') continue;
      if ($el->type == 'js' or !in_array($el->type, $jsTypesAdded)) {
        $jsTypesAdded[] = $el->type;
        $this->jsInline .= $js;
      }
    }
  }

  protected function wrapCols($html) {
    if (!strstr($html, 'type_col')) return $html;
    $n = 0;
    foreach ($this->els as $v) if ($v['type'] == 'col') $n++;
    return preg_replace('/(<\!-- Open fields(?:[^>]*)-->(?:.*)<\!-- Close fields(?:[^>]*)-->)/sm', '<div class="colSet colN'.$n.'">$1<div class="clear"><!-- --></div></div>', $html);
  }

  public $disableJs = false;

  function js() {
    if ($this->disableJs or $this->disableFormTag) return '';
    $this->js = $this->jsInline = '';
    $jsTypesAdded = [];
    $typeJs = '';
    foreach ($this->els as $el) {
      /* @var $el FieldEAbstract */
      if (($js = $el->jsInline()) != '') $this->jsInline .= $js;
      if (($js = $el->js())) $this->js .= $js;
      if (($js = $el->typeJs()) and !in_array($el->type, $jsTypesAdded)) {
        $jsTypesAdded[] = $el->type;
        $typeJs .= $js;
      }
    }
    $this->js .= $typeJs;
    // Call "js..." methods
    foreach (get_class_methods($this) as $method) {
      if ($method != 'js' and substr($method, 0, 2) == 'js') {
        if (($c = $this->$method()) != '') {
          if (Misc::hasPrefix('jsInline', $method)) $this->jsInline .= "\n// -- $method -- \n".$c;
          else $this->js .= "\n// -- $method -- \n".$c;
        }
      }
    }
    $r = '';
    if (($url = $this->getCachedJsUrl()) !== false) $r .= "\n<div id=\"{$this->id()}js\" style=\"display:none\">$url</div>";
    if ($this->jsInlineDynamic) $this->jsInline .= "\n".$this->jsInlineDynamic;
    if ($this->jsInline) $r .= "\n<div id=\"{$this->id()}jsInline\" style=\"display:none\">{$this->jsInline}</div>";
    return $r;
  }

  protected function getCachedJsUrl() {
    if ($this->js == '') return false;
    Dir::make(UPLOAD_PATH.'/js/cache/form');
    $file = UPLOAD_PATH.'/js/cache/form/'.$this->id().'.js';
    if (getConstant('FORCE_STATIC_FILES_CACHE') or !file_exists($file)) {
      file_put_contents($file, "Ngn.frm.init.{$this->id()} = function() {\n{$this->js}\n};\n");
    }
    return '/'.UPLOAD_DIR.'/js/cache/form/'.$this->id().'.js?'.(getConstant('FORCE_STATIC_FILES_CACHE') ? Misc::randString() : filemtime($file));
  }

  function display() {
    print $this->html();
  }

  function getValues() {
    if (!$this->_rows) return false;
    foreach ($this->_rows as $v) if ($v['value']) $values[$v['name']] = $v['value'];
    return $values;
  }

  protected function _initErrors() {
  }

  protected function initErrors() {
    foreach ($this->dependRequire as $v) {
      $dependEl = $this->getElement($v[0]);
      if ($dependEl->value()) {
        $requireEl = $this->getElement($v[1]);
        if (!$requireEl->value()) {
          $requireEl->error("Если поле <b>{$dependEl['title']}</b> заполнено, то и это должно быть");
        }
      }
    }
  }

  final function validate() {
    if (!$this->isSubmitted()) return false; // throw new Exception('Validation mast be used only with submitting');
    $this->lastError = false;
    $this->_initErrors();
    $this->initErrors();
    foreach ($this->els as $el) {
      /* @var $el FieldEAbstract */
      if (!$el->validate()) {
        if (empty($el->error)) throw new Exception('error is empty. $el: '.getPrr($el));
        $this->lastError = $el->error;
        $this->hasErrors = true;
      }
    }
    if (isset($this->globalError)) {
      $this->lastError = $this->globalError;
      $this->hasErrors = true;
    }
    if ($this->hasErrors and !$this->lastError) throw new Exception("Last error is empty. Form has errors bun don't has last error. It's strange");
    return !$this->hasErrors;
  }

  public $lastError;

  public $tplRequired = '&nbsp;<b style="color: #FF0000;">*</b>';

  protected $nameArray;

  /**
   * Устанавливает имя массива всех input полей
   *
   * @param string
   */
  function setNameArray($nameArray) {
    $this->nameArray = $nameArray;
  }

  private $equality;

  function setEquality($name, $value) {
    $this->equality[$name] = $value;
  }

  protected function headerOpened($elementDepth) {
    return !empty($this->headerOpened[$elementDepth]);
  }

  protected function closeAllOpenedHeaders($comments) {
    $html = '';
    if (($l = count($this->headerOpened)) != 0) {
      // Закрываем открытые заголовочные блоки начиная с большей глубины
      for ($depth = $l - 1; $depth >= 0; $depth--) {
        if ($this->headerOpened($depth)) {
          $html .= $this->htmlHeaderGroupClose($depth, $comments);
        }
      }
    }
    return $html;
  }

  function createCloseHeaderGroup($depth) {
    $this->createElement([
      'type' => 'html',
      'html' => $this->htmlHeaderGroupClose($depth, 'direct close')
    ]);
  }

  protected function htmlHeader(FieldEHeaderAbstract $el) {
    if (!$this->isHeaderGroupTags) return false;
    $this->curHeaderId = $el['id'];
    $t = '';
    if ($el['depth'] == 0) $t = $this->closeAllOpenedHeaders('open header depth 0');
    $this->setHeaderOpened($el['depth'], true);
    $tt = str_replace('{name}', $el['name'], $this->templates['headerOpen']);
    $tt = str_replace('{class}', ' type_'.$el->type, $tt);
    $html = str_replace('{required}', $this->templates['required'], $el->html());
    return $t."<!-- Open fields group depth={{$el['depth']}}, type={{$el->type}}, id={{$this->curHeaderId}} -->\n".$tt.$html;
  }

  protected $n = 1;

  /**
   * @param array
   * @return FieldEAbstract
   */
  function createElement(array $d) {
    if (!empty($d['name']) and isset($this->nameArray)) $d['name'] = $this->nameArray.'['.$d['name'].']'; // check
    if (empty($d['type'])) $d['type'] = 'text';
    if (empty($d['name'])) {
      $d['name'] = 'el'.$this->n;
      $this->n++;
    }
    if ($this->options['placeholders'] and !empty($d['title'])) $d['placeholder'] = $d['title'];
    if (isset($this->els[$d['name']])) {
      throw new Exception('Field with name "'.$d['name'].'" already exists in <b>$this->els</b>. existing: '.getPrr($this->els[$d['name']]->options).', trying to create: '.getPrr($d));
    }
    if (isset($d['maxlength']) and $d['maxlength'] == 0) unset($d['maxlength']);
    if (isset($this->options['fieldOptions'][$d['name']])) $d = array_merge($d, $this->options['fieldOptions'][$d['name']]);
    $this->els[$d['name']] = $el = FieldCore::get($d['type'], $d, $this);
    if (isset($el->inputType) and $el->inputType == 'file') $this->encType = 'multipart/form-data';
    return $el;
  }

  function deleteElement($name) {
    Arr::dropCallback($this->els, function ($v) use ($name) {
      $v->options['name'] == $name;
    });
  }

  /**
   * Возвращает значения значимых полей
   *
   * @return array
   */
  function getData() {
    $r = [];
    foreach ($this->getElements() as $name => $el) {
      if ($el['noValue']) continue;
      // Если в элементе или форме есть флаг 'filterEmpties' и значение элемента пусто
      if ((!empty($this->options['filterEmpties']) or !empty($el['filterEmpties'])) and $el->isEmpty()) continue;
      $value = $el->value();
      BracketName::setValue($r, $name, $value === null ? '' : $value);
    }
    return $r;
  }

  /**
   * Возвращает значения значимых полей
   *
   * @return array
   */
  function getAllData() {
    $r = [];
    foreach ($this->getElements() as $name => $el) {
      $value = $el['value'];
      BracketName::setValue($r, $name, $value === null ? '' : $value);
    }
    return $r;
  }

  protected $_id;

  /**
   * @return string Уникальный идентификатор формы
   */
  function id() {
    $this->callOnce('initId');
    return $this->_id;
    if (empty($_POST)) ProjectData::update('Fuk', $this->fields->getFields());
    if (!empty($_POST)) ProjectData::update('Fak', $this->fields->getFields());
    return 'f'.md5(serialize($this->fields->getFields()));
    /*
    if (isset($this->lastFields) and ($this->lastFields != $this->fields->getFields())) {
      $ff = $this->fields->getFields();
      foreach ($ff as $k => $v) if ($v != $this->lastFields[$k]) die2([$v, $this->lastFields[$k]]);
      //die2([$this->lastFields, $this->fields->getFields()]);
    }
    $this->lastFields = $this->fields->getFields();
    return $this->_id;
    */
  }

  protected function init() {
  }

  protected function defineOptions() {
    return [
      'placeholders' => false,
      'submitTitle'  => 'Сохранить'
    ];
  }

  private function initFsb() {
    if ($this->enableFsb) {
      if ($this->fsb) return;
      $this->fsb = new FormSpamBotBlocker;
      $this->fsb->hasSession = false;
      $this->nospam = false; // Если FSBB включен, определяем включаем флаг отсутствия спама
    }
    else {
      $this->nospam = true;
    }
  }

  /**
   * @var string Action Field
   */
  protected $defaultActionName = 'action';

  protected $hiddenFields = [];

  protected $actionFieldValue;

  function setActionFieldValue($v) {
    $this->actionFieldValue = $v;
  }

  function addNoValueHiddenField($data) {
    if (!isset($data['name'])) {
      throw new Exception('Name not defined in: '.getPrr($data));
    }
    if (($v = Arr::getValueByKey($this->hiddenFields, 'name', $data['name']))) {
      throw new Exception("{$data['name']} already exists: {$v['value']}. Trying to replace by '{$data['value']}'");
    }
    $data['type'] = 'hidden';
    $data['noValue'] = true;
    $this->hiddenFields[] = $data;
  }

  protected function initId() {
    $this->_id = 'f'.md5(serialize($this->fields->getFields()));
  }

  function addField(array $v, $after = false) {
    $this->fields->addField($v, $after);
    $this->initId();
  }

  protected function initDefaultHiddenFields() {
    $this->addNoValueHiddenField([
      'name'  => 'formId',
      'value' => $this->id()
    ]);
    if (!empty($this->actionFieldValue)) {
      $this->addNoValueHiddenField([
        'name'  => $this->defaultActionName,
        'value' => $this->actionFieldValue
      ]);
    }
    if (isset($_SERVER['HTTP_REFERER'])) {
      $this->addNoValueHiddenField([
        'name'  => 'referer',
        'value' => $_SERVER['HTTP_REFERER']
      ]);
    }
  }

  public $defaultElements;

  protected function initDefaultElements() {
    if ($this->disableFormTag) return;
    $this->callOnce('initDefaultHiddenFields');
    foreach ($this->hiddenFields as $v) $this->createElement($v);
    if (!empty($this->defaultElements)) {
      foreach ($this->defaultElements as $v) {
        $this->createElement($v);
      }
    }
  }

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

  /**
   * Значение отформатировано
   *
   * @var bool
   */
  public $valueFormated = false;

  protected function initElements($reset = false) {
    if ($reset) {
      $this->els = [];
      $this->initDefaultElements();
    }
    if ($this->elementsInitialized and !$reset) return;
    $this->elementsInitialized = true;
    $this->hasErrors = false;
    if ($this->onlyRequired) {
      $fields = $this->fields->getRequired();
    }
    else {
      $fields = $this->fields->getFieldsF();
    }
    foreach ($fields as $n => $v) {
      if (!is_array($v)) throw new Exception("Field #$n is not array");
      if ($this->fields->isFileType($v['name'])) {
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
   * Определяет данные полей и создаёт объекты элементов формы
   *
   * @manual form
   * @param   array Значения по умолчанию
   * @return  array
   */
  function setElementsData(array $defaultData = [], $reset = true) {
    $this->defaultData = $defaultData;
    $this->elementsData = $defaultData;
    if ($this->isSubmitted() and $this->fromRequest) {
      $this->elementsData = $this->req->p;
    }
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
    $this->setElementsData($this->getDefaultData());
    $this->elementsDefaultDefined = true;
    return true;
  }

  /**
   * Выводит только указанные для инициализации поля
   *
   * @param   bool Флаг
   */
  function outputOnlyFields($flag = true) {
    $this->disableFormTag = $flag;
    $this->disableSubmit = $flag;
    $this->disableJs = $flag;
  }

  function fsb() {
    if ($this->hasErrors) return;
    $this->initFsb();
    // Проверяем на спам, если есть сабмит и добавляем ошибку, если проверку не прошла
    if ($this->enableFsb and $this->isSubmitted()) {
      // Ах да.. только в том случае, если засабмичено
      //if ($this->defaultData) throw new Exception('default data not exists');
      $this->nospam = $this->fsb->checkTags($this->defaultData);
      if (!$this->nospam) {
        $this->globalError('Не прошла проверка на спам. <a href="'.Tt()->getPath().'">Попробуйте заполнить форму ещё раз</a>');
      }
    }
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
   * @param   string Имя заголовочного поля, открывающее секцию или обычного поля
   * @param   string Имя поля, от которого зависит отображать ли секцию
   * @param   string Условие отображения в формате "v == 4" (javascript),
   *                  где $v - текущее значение поля $condFieldName
   * @param   string  header/field
   *
   */
  function addVisibilityCondition($sctionName, $condFieldName, $jsCond, $type = 'header') {
    $this->visibilityConditions[] = [
      'headerName'    => $sctionName,
      'condFieldName' => $condFieldName,
      'cond'          => $jsCond,
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
    foreach ($this->els as $el) prr($el->options);
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
        'title' => $this->fields->fields[$name]['title'],
        'value' => $el->titledValue()
      ];
    }
    return $r;
  }

  function hasAttachebleWisiwig() {
    return Arr::getValueByKey($this->fields->fields, 'type', 'wisiwig') !== false;
  }

}
