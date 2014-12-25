<?php

/**
 * options[currentFile] - текущий файл (путь относительно UPLOAD_PATH), находящийся в этом поле
 * options[value] - загруженный в результате поста
 * options[postValue] - отсюда беруться данные для сохранения данных. Например: DmfaFile
 */
class FieldEFile extends FieldEFileBase {

  function defineOptions() {
    return array_merge(parent::defineOptions(), [
      'value'            => null,
      'filterEmpties'    => true,
      'currentFileTitle' => 'Текущий файл',
      'postValue'        => null,
      'multiple'         => null,
      'allowedMimes'     => null
    ]);
  }

  protected function init() {
    parent::init();
    if (($v = $this->valueToProcess()) !== null) {
      !$this->options['multiple'] ? $this->processSingle($v) : $this->processMultiple($v);
    }
  }

  protected function allowedTagParams() {
    return array_merge(parent::allowedTagParams(), ['accept']);
  }

  /**
   * Загруженное, но не сохраненное значение
   */
  protected function valueToProcess() {
    if (!$this->form->fromRequest) return $this->options['value'];
    $files = isset($this->form->options['files']) ? $this->form->options['files'] : $this->form->req->files;
    $value = BracketName::getValue($files, $this->options['name']);
    if (!empty($value['error'])) return null;
    return $value;
  }

  /**
   * Сохраненное текущее значение
   */
  protected function dataValue() {
    if (!$this->form->fromRequest) return '';
    return $this->options['value'];
  }

  protected function dataRealValue() {
    return WEBROOT_PATH.$this->dataValue();
  }

  protected function postValue() {
    return $this->options['postValue'];
  }

  /**
   * Значение для отображения в контроле
   */
  protected function htmlValue() {
    return null;
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
      $this->error = "Неправильный формат файла ($mime). Допускаемые: ".St::enum($this->allowedMimes);
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
      if ($this->options['allowedMimes'] and !in_array($mime, $this->options['allowedMimes'])) {
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
    if ($v = $this->postValue()) {
      $r .= '<div class="clear"></div>';
      if ($this->options['multiple']) {
        foreach ($v as $file) $r .= $this->htmlUploadedLink($file);
      }
      else {
        $r .= $this->htmlUploadedLink($v);
      }
    }
    $r .= '</div>';
    return $r;
  }

  protected function htmlUploadedLink(array $file) {
    if (!file_exists($file['tmp_name'])) return '';
    return "<a class=\"file fileUploaded iconBtnCaption\"><i></i>загружен (".File::format2(filesize($file['tmp_name'])).")</a>";
  }

  function isEmpty() {
    return !$this->postValue();
  }

  function _html() {
    $value = $this->prepareInputValue($this->postValue());
    $params = [
      'name'      => $this->options['name'].($this->options['multiple'] ? '[]' : ''),
      'value'     => '',
      'data-file' => (bool)$value,
    ];
    if ($this->options['allowedMimes']) $params['accept'] = implode(',', $this->options['allowedMimes']);
    if ($this->options['multiple']) $params['multiple'] = null; // null for empty tag param
    return $this->htmlNav().'<input type="file"'.Tt()->tagParams($params).$this->getClassAtr().' />';
  }

}