<?php

/**
 * options[currentFile] - текущий файл (путь относительно UPLOAD_PATH), находящийся в этом поле
 * options[value] - загруженный в результате поста
 */
class FieldEFile extends FieldEFileBase {

  function defineOptions() {
    return array_merge(parent::defineOptions(), ['currentFileTitle' => 'Текущий файл']);
  }

  protected function init() {
    parent::init();
    if ($this->form->fromRequest) {
      $files = isset($this->form->options['files']) ? $this->form->options['files'] : $this->form->req->files;
      $uploadedFileValue = null;
      if (!empty($files)) {
        $uploadedFileValue = BracketName::getValue($files, $this->options['name']);
        empty($this->options['multiple']) ? $this->processSingle($uploadedFileValue) : $this->processMultiple($uploadedFileValue);
      }
    }
    else {
      $uploadedFileValue = !empty($this->options['value']) ? $this->options['value'] : null;
    }
    if ($uploadedFileValue !== null /* and empty($uploadedFileValue['error'])*/) {
      empty($this->options['multiple']) ? $this->process2Single($uploadedFileValue) : $this->process2Multiple($uploadedFileValue);
    }
  }

  protected function processSingle(&$uploadedFileValue) {
    if (!empty($uploadedFileValue['error'])) {
      $uploadedFileValue = null;
      return;
    }
    if (empty($uploadedFileValue['tmp_name'])) {
      throw new EmptyException("{$this->options['name']}: uploadedFileValue['tmp_name']");
    }
    if (!file_exists($uploadedFileValue['tmp_name'])) {
      throw new NoFileException($uploadedFileValue['tmp_name']);
    }
  }

  protected function check($uploadedFileValue) {
    // Если файл загружен
    Arr::checkEmpty($uploadedFileValue, 'tmp_name');
    $mime = File::getMime($uploadedFileValue['tmp_name']);
    Misc::checkEmpty($mime);
    return $mime;
  }

  protected function process2Single(array $uploadedFileValue) {
    $mime = $this->check($uploadedFileValue);
    if (!empty($this->allowedMimes) and !in_array($mime, $this->allowedMimes)) {
      // Если для этого поля определены MIME и MIME загруженного 
      // файла на присутствует в этом списке
      $this->error = "Неправильный формат файла ($mime). Допускаемые: ".Tt()->enum($this->allowedMimes);
    }
    else {
      // 1 состояние
      $this->options['postValue'] = $uploadedFileValue;
      // всё остальное - 2-е
    }
  }

  protected function processMultiple(&$uploadedFileValue) {
    if (empty($uploadedFileValue)) return;

    foreach ($uploadedFileValue as &$v) $this->processSingle($v);
  }

  protected function process2Multiple(array &$uploadedFileValue) {
    foreach ($uploadedFileValue as $k => $v) {
      $mime = $this->check($v);
      if (!empty($this->allowedMimes) and !in_array($mime, $this->allowedMimes)) {
        unset($uploadedFileValue[$k]);
      }
    }
    $this->options['postValue'] = $uploadedFileValue;
  }

  protected function validate1() {
    if (empty($this->options['value']) and
      empty($this->options['postValue']) and
      !empty($this->options['required'])
    ) {
      $this->error = "Поле «".(empty($this->options['title']) ? $this->options['name'] : $this->options['title'])."» обязательно для заполнения";
    }
  }

  protected function getCurrentValue() {
    return '/'.UPLOAD_DIR.'/'.$this->options['value'];
  }

  protected function htmlNav() {
    return (empty($this->options['value']) ? '' : '<div class="iconsSet fileNav">'.'<a href="'.$this->getCurrentValue().'" class="'.$this->options['currentFileClass'].'" target="_blank"><i></i> '.$this->options['currentFileTitle'].'</a>'.((!empty($this->form->options['deleteFileUrl']) and empty($this->options['required'])) ? '<a href="'.$this->form->options['deleteFileUrl'].'&fieldName='.$this->options['name'].'" class="delete confirm" title="Удалить"><i></i></a>' : '').'</div>');
  }

  function _html() {
    $params = [
      'name'      => $this->options['name'],
      'value'     => $this->options['value'],
      'data-file' => $this->options['value'],
    ];
    if (!empty($this->options['multiple'])) $params['multiple'] = null;
    return $this->htmlNav().'<input type="file" '.Tt()->tagParams($params).$this->getClassAtr().' />';
  }

  function value() {
    return empty($this->options['postValue']) ? $this->options['value'] : $this->options['postValue'];
  }

}