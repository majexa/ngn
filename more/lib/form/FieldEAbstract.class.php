<?php

/**
 * Каждая из частей этого поля срабатывает в разные моменты
 *
 * 1. init(), например при создании поля, тогда, когда значение поля ещё не заполнено
 * в этом случае можно определить значения по умолчанию.
 * В случае же с файлом в init() происходит заполнение конечного значения поля из массива _FILES,
 * т.к. в _POST этого значения нет.
 * Значение поля находится в $this->options['value']
 *
 * $this->options['filterEmpties'] если метод isEmpty возвращает true значение элемента не учитывается в
 * данных возвращаеммых формой
 *
 *
 * inputValue - нужен для передачи в пост
 * value - нужен для внутреннего использования в контроле. формат контрола
 *
 */
abstract class FieldEAbstract extends ArrayAccesseble {
use Options;

  public $type;

  public $error = false;

  /**
   * @var Form
   */
  public $form;

  public $valueChanged = false;

  protected $defaultValue;

  protected function defineOptions() {
    return [
      'useTypeJs' => false, // Использовать typeJs
      'required' => false // обязательное для заполнения
    ];
  }

  protected function &getArrayRef() {
    return $this->options;
  }

  protected static $n;

  function __construct(array $options = [], Form $form = null) {
    $this->form = $form;
    if ($this->form and ($class = $this->allowedFormClass())) {
      if (!isset($this->form)) throw new Exception('$this->form must be defined');
      if (!is_a($this->form, $class)) throw new Exception('Form object must be an instance of "'.$class.'" class');
    }
    // default options
    $this->options['depth'] = 0;
    $this->setOptions($options);
    if (method_exists($this, 'beforeInit')) $this->beforeInit();
    $this->init();
    if (isset($this->form) and !$this->form->valueFormated and ($value = $this->formatValue())) {
      $this->options['postValue'] = $this->options['value'];
      $this->options['value'] = $value;
    }
    $this->initDefaultValue();
    if (empty($this->options['name'])) {
      $this->options['name'] = 'el'.self::$n;
      self::$n++;
    }
  }

  /**
   * Используется в том случае, если в контроле необходимо использовать отличное
   * от исходного значение
   */
  protected function formatValue() {
    return false;
  }

  /**
   * Возвращает текущее значение поля для сохранения
   */
  function value() {
    if (!empty($this->options['noValue'])) return null;
    return $this->postValue();
  }

  protected function postValue() {
    if (isset($this->options['postValue'])) return $this->options['postValue'];
    return isset($this->options['value']) ? $this->options['value'] : null;
  }

  function titledValue() {
    return $this->value();
  }

  // --

  protected function prepareValue() {
    if (!empty($this->options['noValue'])) return;
    if ($this->isEmpty() and isset($this->options['default'])) $this->options['value'] = $this->options['default'];
    if (!empty($this->options['value']) and is_string($this->options['value'])) $this->options['value'] = trim($this->options['value']);
  }

  protected $staticType = null;

  /**
   * @var string Имя поля с вырезанными скобками
   */
  public $baseName;

  protected function init() {
    if (!isset($this->options['name'])) {
      die2($this->options);
    }
    if (empty($this->options['id'])) {
      $this->options['id'] = Misc::name2id($this->options['name']);
    }
    if ($this->isEmpty() and empty($this->options['value'])) {
      $this->options['value'] = null;
    }
    //if (!isset($this->options['type'])) throw new Exception('type is not defined in '.get_class($this));
    if (isset($this->options['type']) and $this->options['type'] == 'hidden') $this->options['noTypeCss'] = true;
    if (empty($this->options['help'])) $this->options['help'] = '';
    $this->baseName = BracketName::getPureName($this->options['name']);
    $this->prepareValue();
    if (!empty($this->staticType)) $this->type = $this->staticType;
    else
      $this->type = lcfirst(Misc::removePrefix('FieldE', get_class($this)));
    if ($this->form and $this->form->isSubmitted()) {
      if ($this->form->create) {
        $this->valueChanged = true;
      }
      else {
        $defValue = BracketName::getValue($this->form->defaultData, $this->options['name']);
        // Если поле до поста было не пустым
        if ($defValue !== null and $defValue != $this->options['value']) {
          $this->valueChanged = true;
        }
      }
    }
    $this->addRequiredCssClass();
  }

  protected function allowedFormClass() {
    return false;
  }

  protected function addRequiredCssClass() {
    if (!empty($this->options['required'])) {
      $this->cssClasses[] = 'required';
    }
  }

  function isEmpty() {
    if (!isset($this->options['value'])) return true;
    return Arr::isEmpty($this->options['value']);
  }

  /**
   * @api
   * Валидация. Происходит только при сабмите формы.
   *
   * Ищет методы текущего объекта, имеющие вид `validate{N}` и вызывает их по очереди.
   *
   * Если после вызова такого метода, свойство `FieldEAbstract::$error` заполнено,
   * значит проверка прерывается, а метод `validate()` возвращает `FALSE`.
   *
   * `N` должно быть > `1`, т.к. уже существует один стандартный метод
   * валидации `FieldEAbstract::validate1()`.
   *
   * Он проверяет пустое ли значение _Элемента поля_, если опция `required = true`.
   */
  function validate() {
    if (!empty($this->error)) return false;
    $n = 1;
    if (!empty($this->options['validator'])) {
      foreach (Misc::quoted2arr($this->options['validator']) as $name) {
        if (($error = O::get(ClassCore::nameToClass('FieldV', $name))->error($this->options['value'])) !== false) {
          $this->error = $error;
        }
      }
    }
    $method = 'validate'.$n;
    while (method_exists($this, $method)) {
      if ($n > 1 and empty($this->options['value'])) break;
      $this->$method();
      if (!empty($this->error)) break;
      $n++;
      $method = 'validate'.$n;
    }
    return empty($this->error);
  }

  protected function validate1() {
    if (!empty($this->options['required']) and empty($this->options['value']) and empty($this->options['disabled'])) {
      $this->error = "Поле «{$this->options['title']}» обязательно для заполнения";
    }
  }

  function error($text) {
    $this->error = $text;
  }

  protected function initDefaultValue() {
  }

  /**
   * @api
   * Возвращает HTML-код _Элемента поля_
   */
  function html() {
    if (isset($this->defaultValue) and !isset($this->options['value'])) $this->options['value'] = $this->defaultValue;
    return $this->_html();
  }

  function _html() {
    return '';
  }

  /**
   * @api
   * Возвращает JS-код _Элемента поля_
   */
  function js() {
    $js = '';
    if (!empty($this->options['jsOptions'])) {
      $js .= 'Ngn.Form.elOptions.'.$this->options['name'].' = '.json_encode($this->options['jsOptions'])."\n";
    }
    $js = $js.$this->_js();
    return $js;
  }

  function jsInline() {
    return '';
  }

  function _js() {
    return '';
  }

  protected $cssClasses = [];

  protected function getCssClasses() {
    if (isset($this->options['cssClass'])) return array_merge($this->cssClasses, [$this->options['cssClass']]);
    return empty($this->cssClasses) ? false : $this->cssClasses;
  }

  function typeCssAndJs() {
    if (empty($this->options['noTypeCss'])) Sflm::frontend('css')->addStaticLib("formEl/$this->type.css");
    if (!isset($this->options['useTypeJs'])) throw new Exception(get_class($this).'::defineOptions does not extends parent');
    if (!$this->options['useTypeJs']) return '';
    Sflm::frontend('js')->addLib("formEl/$this->type", false);
    Sflm::frontend('js')->addClass('Ngn.Form.ElInit.'.ucfirst($this->type), "$this->type field init");
    Sflm::frontend('js')->addClass('Ngn.Form.El.'.ucfirst($this->type), "$this->type field init");
    if (!$this->form) return '';
    return "\n// -- type: {$this->type} --\nnew Ngn.Form.ElInit.factory(Ngn.Form.forms.{$this->form->id()}, '{$this->type}');\n";
  }

  public $errorBacktrace;

  function __set($name, $v) {
    if ($name == 'error') $this->errorBacktrace = getBacktrace();
    $this->$name = $v;
  }

  function postRowHtml() {
    return '';
  }

}