<?php

abstract class FieldEFieldSetAbstract extends FieldEAbstract {

  static $requiredOptions = ['name'];

  public $options = [
    'noRowHtml'     => true,
    'noValue'       => true,
    'jsOptions'     => [],
    // значит поле не создает само никаких данных (по его имени),
    // но тем не менее может эти данные создавать и соответственно
    // возвращаться функцией value()
    'filterEmpties' => true,
    'firstIndexNumber' => 0,

    //'addTitle',
    //'deleteTitle',
    //'cleanupTitle'
  ];

  protected $fields;

  protected $els = [];

  function __construct(array $options = [], Form $form = null) {
    parent::__construct($options, $form);
  }

  protected function init() {
    parent::init();
    $files = BracketName::getValue($this->oForm->req->files, $this->options['name']);
    if (empty($this->options['value']) and $files === null) {

      // Создаем значение по умолчанию
      foreach (Arr::get($this->options['fields'], 'name') as $key) $emptyValue[$key] = '';
      $this->options['value'][$this->options['firstIndexNumber']] = $emptyValue;
    }
    $depth = ++$this->options['depth'];
    $this->oForm->createElement([
      'type'     => empty($this->options['headerToggle']) ? 'header' : 'headerToggle',
      'depth'    => $depth,
      'title'    => isset($this->options['title']) ? $this->options['title'] : '',
      'required' => !empty($this->options['required'])
    ]);
    if (!empty($this->options['help'])) {
      $this->oForm->createElement([
        'type' => 'staticText',
        'text' => $this->options['help']
      ]);
    }
    $this->oForm->createElement([
      'type' => 'html',
      'html' => '<div class="fieldSet type_'.$this->type.'">'
    ]);
    $oFields = new Fields($this->options['fields']);
    // Генерируем поля по данным, если значение определено
    if (!empty($this->options['value'])) {
      // $this->options['value'] - значение взятое из поста
      $itemKeys = array_keys($this->options['value']);
    }
    elseif ($files !== null) {
      // в случае, если в форме только поля файлов
      $itemKeys = array_keys($files);
    }
    if (isset($itemKeys)) {
      foreach ($itemKeys as $n) {
        $this->oForm->createElement([
          'type'  => 'header',
          'depth' => $depth + 1
        ]);
        foreach ($oFields->getFields() as $v) {
          $name = $v['name'];
          $v = $this->addFieldData($v);
          $v['name'] = $this->getName($n, $name);
          $v['filterEmpties'] = $this->options['filterEmpties'];
          $v['value'] = $oFields->isFileType($name) ? BracketName::getValue($this->oForm->defaultData, $v['name']) : BracketName::getValue($this->oForm->elementsData, $v['name']);
          BracketName::setValue($this->oForm->elementsData, $v['name'], $this->oForm->createElement($v)->value());
        }
        $this->oForm->createElement([
          'type'  => 'headerClose',
          'depth' => $depth + 1
        ]);
      }
    }
    else {
      throw new Exception('this block is not realized');
      // Или выводим одну группу полей, если не определено
      $this->oForm->createElement([
        'type'  => 'header',
        'depth' => $depth + 1
      ]);
      // Пост с формы должен обязательно содержать массив с именем филдсета, кол-во элементов
      // в котором было бы равно кол-ву элементов филдсета на html-форме. Необходимо в случае,
      // если филдсет содержит только элементы файлов
      $this->oForm->createElement([
        'type'  => 'hidden',
        'name'  => $this->options['name'].'[dummy][0]',
        'value' => 1
      ]);
      foreach ($this->options['fields'] as $v) {
        $v['name'] = $this->getName(0, $v['name']);
        $v['depth'] = $depth + 1;
        $v['filterEmpties'] = $this->options['filterEmpties'];
        $this->oForm->createElement($v);
      }
      $this->oForm->createElement([
        'type'  => 'headerClose',
        'depth' => $depth + 1
      ]);
    }
    $this->oForm->createElement([
      'type' => 'html',
      'html' => '<div class="clear"><!-- --></div></div>'
    ]);
    $this->oForm->createElement([
      'type'  => 'headerClose',
      'depth' => $depth
    ]);
  }

  protected function addFieldData(array $v) {
    return $v;
  }

  function _js() {
    $this->options['jsOptions']['rowElementSelector'] = '.hgrp';
    return "
var id = '{$this->oForm->id()}';
Ngn.Form.forms[id].eForm.getElements('.type_{$this->type}').each(function(el){
  new Ngn.frm.FieldSet(Ngn.Form.forms[id], el, ".Arr::jsObj($this->options['jsOptions']).");
});
";
  }

  abstract protected function getName($n, $name);

}