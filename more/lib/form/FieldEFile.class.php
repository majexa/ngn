<?php

/**
 * options[currentFile] - текущий файл (путь относительно UPLOAD_PATH), находящийся в этом поле
 * options[value] - загруженный в результате поста
 * options[postValue] - отсюда беруться данные для сохранения данных. Например: DmfaFile
 */
class FieldEFile extends FieldEFileBase {

  /**
   * Значение для отображения в контроле
   */
  protected function htmlValue() {
    // $this->
    return null;
    /*
    if (empty($this->options['value'])) return false;
    die2($this->options['value']);
    $file = UPLOAD_PATH.'/'.$this->options['value'];
    if (!file_exists($file)) return false;
    return '/'.UPLOAD_DIR.'/'.$this->options['value'].'?'.filemtime(UPLOAD_PATH.'/'.$this->options['value']);
    */
  }

  function defineOptions() {
    return array_merge(parent::defineOptions(), [
      'filterEmpties'    => true,
      'currentFileTitle' => 'Текущий файл',
      'postValue'        => null
    ]);
  }

  /**
   * Загруженное, но не сохраненное значение
   */
  protected function valueToProcess() {
    if (!$this->form->fromRequest) {
      return $this->options['value'];
    };
    $files = isset($this->form->options['files']) ? $this->form->options['files'] : $this->form->req->files;
    if (!$files) return null;
    $value = BracketName::getValue($files, $this->options['name']);
    if (!empty($value['error'])) return null;
    return $value;
  }

  /**
   * Сохраненное текущее значение
   */
  protected function dataValue() {
    if (!$this->form->fromRequest) return '';
    //if (!$this->form->fromRequest) throw new Exception('"dataValue" is not supported for non-request usage');
    return $this->options['value'];
  }

  protected function dataRealValue() {
    return WEBROOT_PATH.$this->dataValue();
  }

  protected function postValue() {
    return $this->options['postValue'];
  }

  /*
  Определенное вручную значение поля (минуя Request)
  protected function directValue() {
    if ($this->form->fromRequest) return false;
    return $this->options['value'];
  }
   */

  protected function init() {
    parent::init();
    if (($v = $this->valueToProcess()) !== null) {
      empty($this->options['multiple']) ? $this->processSingle($v) : $this->processMultiple($v);
    }
  }

  protected function _processSingle(&$uploadedFileValue) {
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

  protected function processSingle(array $uploadedFileValue) {
    $mime = $this->check($uploadedFileValue);
    if (!empty($this->allowedMimes) and !in_array($mime, $this->allowedMimes)) {
      // Если для этого поля определены MIME и MIME загруженного 
      // файла на присутствует в этом списке
      $this->error = "Неправильный формат файла ($mime). Допускаемые: ".Tt()->enum($this->allowedMimes);
    }
    else {
      $this->options['postValue'] = $uploadedFileValue;
    }
  }

  protected function _processMultiple(&$uploadedFileValue) {
    if (empty($uploadedFileValue)) return;
    foreach ($uploadedFileValue as &$v) $this->_processSingle($v);
  }

  protected function processMultiple(array &$uploadedFileValue) {
    foreach ($uploadedFileValue as $k => $v) {
      $mime = $this->check($v);
      if (!empty($this->allowedMimes) and !in_array($mime, $this->allowedMimes)) {
        unset($uploadedFileValue[$k]);
      }
    }
    $this->options['postValue'] = $uploadedFileValue;
  }

  protected function validate1() {
    if (empty($this->options['value']) and empty($this->options['postValue']) and !empty($this->options['required'])
    ) {
      $this->error = "Поле «".(empty($this->options['title']) ? $this->options['name'] : $this->options['title'])."» обязательно для заполнения";
    }
  }

  protected function htmlNav() {
    $r = '<div class="iconsSet fileNav">';
    if (($v = $this->dataValue())) {
      $size = File::format2(filesize($this->dataRealValue()));
      $deleteHtml = ((!empty($this->form->options['deleteFileUrl']) and empty($this->options['required'])) ? //
        '<a href="'.$this->form->options['deleteFileUrl'].'&fieldName='.$this->options['name']. //
        '" class="iconBtn delete confirm" title="Удалить сохраненный файл"><i></i></a>' : //
        '');
      $r .= "<a href=\"$v\" class=\"file fileSaved iconBtnCaption\" target=\"_blank\"><i></i>сохранён ($size)</a>$deleteHtml";
    }
    if (($v = $this->postValue()) and file_exists($v['tmp_name'])) {
      $r .= '<div class="clear"></div>';
      $r .= "<a class=\"file fileUploaded iconBtnCaption\"><i></i>загружен (".File::format2(filesize($v['tmp_name'])).")</a>";
      $r .= '<a href="'.$this->form->options['deleteFileUrl'].'&fieldName='.$this->options['name']. //
        '" class="iconBtn delete confirm" title="Удалить загруженный файл"><i></i></a>';

    }
    $r .= '</div>';
    return $r;
  }

  //protected function prepareInputValue() {
  //  if (($value = $this->postValue()) === null) return '';
  //  return $value['tmp_name'];
  //}

  function isEmpty() {
    return !$this->postValue();
  }

  function _html() {
    $value = $this->prepareInputValue($this->postValue());
    $params = [
      'name'      => $this->options['name'],
      'value'     => '',
      'data-file' => (bool)$value,
    ];
    if (!empty($this->options['multiple'])) $params['multiple'] = null;
    return $this->htmlNav().'<input type="file"'.Tt()->tagParams($params).$this->getClassAtr().' />';
  }

}