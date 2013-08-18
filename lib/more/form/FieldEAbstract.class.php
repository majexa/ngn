<?php

/**
 *
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

  public $isPostValue = false;

  protected $useDefaultJs = false;

  protected function defineOptions() {
    return [
      'required' => false
    ];
  }

  protected function allowedFormClass() {
    return false;
  }

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
    if ($this->form and !empty($this->options['name'])) {
      if (!$this->form->req) die2($this->form->req);
      $this->isPostValue = (BracketName::getValue($this->form->req->p, $this->options['name'], BracketName::modeFalse) !== false);
    }
    if ($this->isPostValue) $this->preparePostValue();
    $this->initDefaultValue();
  }

  protected function &getArrayRef() {
    return $this->options;
  }

  protected function preparePostValue() {
  }

  protected function prepareValue() {
    if (!empty($this->options['noValue'])) return;
    if ($this->isEmpty() and isset($this->options['default'])) $this->options['value'] = $this->options['default'];
    if (!empty($this->options['value']) and is_string($this->options['value'])) $this->options['value'] = trim($this->options['value']);
  }

  protected $staticType;

  /**
   * @var Имя поля с вырезанными скобками
   */
  protected $baseName;

  protected function init() {
    if (empty($this->options['id'])) {
      $this->options['id'] = Misc::name2id($this->options['name']);
    }
    if ($this->isEmpty() and empty($this->options['value'])) {
      $this->options['value'] = null;
    }
    if (empty($this->options['help'])) $this->options['help'] = '';
    $this->baseName = BracketName::getPureName($this->options['name']);
    $this->prepareValue();
    if (isset($this->staticType)) $this->type = $this->staticType;
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

  protected function addRequiredCssClass() {
    if (!empty($this->options['required'])) {
      $this->cssClasses[] = 'required';
    }
  }

  /**
   * Возвращает текущее значение поля
   */
  function value() {
    if (!empty($this->options['noValue'])) return null;
    return isset($this->options['value']) ? $this->options['value'] : null;
  }

  function titledValue() {
    return $this->value();
  }

  function isEmpty() {
    if (!isset($this->options['value'])) return true;
    return Arr::isEmpty($this->options['value']);
  }

  /**
   * Вызывается только при сабмите формы
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
    if (!empty($this->options['required']) and empty($this->options['value'])) {
      $this->error = "Поле «{$this->options['title']}» обязательно для заполнения";
    }
  }

  function error($text) {
    $this->error = $text;
  }

  protected function initDefaultValue() {
  }

  function html() {
    if (isset($this->defaultValue) and !isset($this->options['value'])) $this->options['value'] = $this->defaultValue;
    return $this->_html();
  }

  function _html() {
    return '';
  }

  function js() {
    $js = '';
    if (!empty($this->options['jsOptions'])) {
      $js .= 'Ngn.Form.ElOptions.'.$this->options['name'].' = '.Arr::jsObj($this->options['jsOptions'])."\n";
    }
    $js = $js.$this->_js();
    if ($js) $js = "\n// ------- type: {$this->type} -------\n".$js;
    return $js;
  }

  function jsInline() {
    return '';
  }

  function _js() {
    if ($this->useDefaultJs) return $this->defaultJs();
    return '';
  }

  protected $cssClasses = [];

  protected function getCssClasses() {
    if (isset($this->options['cssClass'])) return array_merge($this->cssClasses, [$this->options['cssClass']]);
    return empty($this->cssClasses) ? false : $this->cssClasses;
  }

  protected function defaultJs() {
    Sflm::get('js')->addLib("formEl/$this->type");
    Sflm::get('js')->addClass('Ngn.Form.El.'.ucfirst($this->type));
    Sflm::get('css')->addLib("i/css/formEl/$this->type.css");
    return "\nnew Ngn.Form.ElInit.factory(Ngn.Form.forms.{$this->form->id()}, '{$this->type}');\n";
  }

  public $errorBacktrace;

  function __set($name, $v) {
    if ($name == 'error') $this->errorBacktrace = getBacktrace();
    $this->$name = $v;
  }

}