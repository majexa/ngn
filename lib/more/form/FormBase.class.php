<?php

class FormBase {
use Options;

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

  function __construct(array $options = []) {
    $this->setOptions($options);
    $this->req = empty($this->options['req']) ? O::get('Req') : $this->options['req'];
  }

  function isSubmitted() {
    return !empty($this->req->p);
  }

  protected function elementExists($name) {
    return isset($this->els[$name]);
  }

  /**
   * @param  string  Имя поля
   *
   * @return FieldEAbstract
   */
  function getElement($name) {
    if (!isset($this->els[$name])) return false;
    return $this->els[$name];
  }

  function getElements() {
    if (!isset($this->els)) throw new Exception('Elements not initialized');
    return $this->els;
  }

  protected $globalError;

  function globalError($message) {
    $this->lastError = $this->globalError = 'Ошибка: '.$message;
    $this->hasErrors = true;
  }

  function isSubmittedAndValid() {
    return $this->isSubmitted() and $this->validate();
  }

  protected function dataParams() {
    return false;
  }

  protected function htmlFormOpen() {
    if (!$this->disableFormTag) {
      $html = '<form action="'.($this->action ? $this->action : $this->req->options['uri']).'"';
      if (($data = $this->dataParams())) $html .= Html::dataParams($data);
      if (!empty($this->encType)) $html .= ' enctype="'.$this->encType.'"';
      if (!empty($this->options['name'])) $html .= ' name="'.$this->options['name'].'"';
      $html .= ' id="'.$this->id().'" method="post">';
      return $html;
    }
    else {
      return '';
    }
  }

  protected function htmlElementInput(FieldEAbstract $el, $input) {
    $element = str_replace('{input}', $input, $this->templates['input']);
    $element = str_replace('{name}', $el['name'], $element);
    $element = str_replace('{value}', is_array($el['value']) ? '' : $el['value'], $element);
    $element = str_replace('{id}', $el['id'], $element);
    $element = str_replace('{rowClass}', $this->htmlGetRowClassAtr($el), $element);
    return $element;
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
    if (!$this->headerOpened($elementDepth)) throw new Exception("Header depth={{$elementDepth}} alreay closed. ($comments). html: <pre>$f</pre>");
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

  /**
   * Возвращает HTML формы
   *
   * @return string
   */
  function html() {
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
    if (!$this->disableFormTag) $html .= '</form>';
    return $html.$this->js();
  }

  protected function initElementsInlinsJs() {
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
    $this->js = '';
    $jsTypesAdded = [];
    foreach ($this->els as $el) {
      if (($js = $el->jsInline()) != '') $this->jsInline .= $js;
      if (($js = $el->js()) == '') continue;
      if ($el->type == 'js' or !in_array($el->type, $jsTypesAdded)) {
        $jsTypesAdded[] = $el->type;
        $this->js .= $js;
      }
    }
    // Call "js..." methods
    foreach (get_class_methods($this) as $method) {
      if ($method != 'js' and substr($method, 0, 2) == 'js') {
        if (Misc::hasPrefix('jsInline', $method)) {
          if (($c = $this->$method()) != '') {
            $this->jsInline .= "\n// ------- $method ------- \n".$c;
          }
        }
        elseif (($c = $this->$method()) != '') $this->js .= "\n// ------- $method ------- \n".$c;
      }
    }
    $r = '';
    if (($url = $this->getCachedJsUrl()) !== false) $r .= "\n<div id=\"{$this->id()}js\" style=\"display:none\">$url</div>";
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
    return '/'.UPLOAD_DIR.'/js/cache/form/'.$this->id().'.js?'.(getConstant('FORCE_STATIC_FILES_CACHE') ? Misc::randString() : BUILD);
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

  function validate() {
    if (!$this->isSubmitted()) return false; // throw new Exception('Validation mast be used only with submitting');
    $this->_initErrors();
    $this->initErrors();
    foreach ($this->els as $el) {
      /* @var $el FieldEAbstract */
      if (!$el->validate()) {
        if (empty($el->error)) throw new Exception('error is empty. $el: '.getPrr($el));
        $this->lastError = $el->error; //.(getConstant(IS_DEBUG) ? " (type={$el->type}). \$el: ".getPrr($el) : '');
        $this->hasErrors = true;
      }
    }
    if (isset($this->globalError)) {
      $this->lastError = $this->globalError;
      $this->hasErrors = true;
    }
    return !$this->hasErrors;
  }

  public $lastError;

  function getLastError() {
    return $this->lastError;
  }

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
    if (isset($this->els[$d['name']])) {
      throw new Exception('Field with name "'.$d['name'].'" already exists in <b>$this->els</b>. existing: '.getPrr($this->els[$d['name']]->options).', trying to create: '.getPrr($d));
      //."\nALL:\n".getPrr($this->els));
    }
    if (isset($d['maxlength']) and $d['maxlength'] == 0) unset($d['maxlength']);
    $this->els[$d['name']] = $el = FieldCore::get($d['type'], $d, $this);
    if (isset($el->inputType) and $el->inputType == 'file') $this->encType = 'multipart/form-data';
    return $el;
  }

  function deleteElement($name) {
    Arr::dropCallback($this->els, function($v) use ($name) {
      $v->options['name'] == $name;
    });
  }

  function getData() {
    $r = [];
    foreach ($this->getElements() as $name => $el) {
      if (!empty($el['noValue'])) continue;
      // Если в элементе или форме есть флаг 'filterEmpties' и значение элемента пусто
      if ((!empty($this->options['filterEmpties']) or !empty($el['filterEmpties']))
        and $el->isEmpty()
      ) continue;
      $value = $el->value();
      BracketName::setValue($r, $name, $value === null ? '' : $value);
    }
    return $r;
  }

}