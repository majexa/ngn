<?php

class UploadTemp extends Options2 {

  public $formId, $tempId, $tempFolder;

  protected function defineOptions() {
    return [
      'multiple' => false,
      'tempId'   => session_id()
    ];
  }

  function __construct(array $options = []) {
    parent::__construct($options);
    $this->formId = $this->options['formId'] ? $this->options['formId'] : 'default';
    $this->tempId = $this->options['tempId'] ? $this->options['tempId'] : Misc::randString(8);
    $this->tempFolder = Misc::clearLastSlash(TEMP_PATH.'/upload/'.$this->tempId);
  }

  protected function getI($fieldName) {
    static $a;
    return isset($a[$fieldName]) ? ++$a[$fieldName] : $a[$fieldName] = 0;
  }

  function getFiles() {
    if (!is_dir($this->tempFolder)) return [];
    $files = [];
    $data = db()->query('SELECT * FROM upload_temp WHERE formId=? AND tempId=?', $this->formId, $this->tempId);
    foreach ($data as $v) {
      if (!file_exists($this->tempFolder.'/'.$v['fileName'])) continue;
      if ($v['multiple']) {
        $i = $this->getI($v['fieldName']);
        BracketName::setValue($files, $v['fieldName']."[name][$i]", $v['name']);
        BracketName::setValue($files, $v['fieldName']."[tmp_name][$i]", $this->tempFolder.'/'.$v['fileName']);
        BracketName::setValue($files, $v['fileName']."[size][$i]", filesize($this->tempFolder.'/'.$v['fileName']));
      }
      else {
        BracketName::setValue($files, $v['fieldName'], [
          'name'     => $v['name'],
          'tmp_name' => $this->tempFolder.'/'.$v['fileName'],
          'size'     => filesize($this->tempFolder.'/'.$v['fileName'])
        ]);
      }
    }
    return $files;
  }

  function upload(array $files, $fieldName) {
    if ($this->options['multiple']) $files = $files[$fieldName];
    foreach ($files as $v) $this->uploadFile($v, $fieldName);
  }

  function uploadFile(array $file, $fieldName) {
    Arr::checkEmpty($file, ['tmp_name', 'name']);
    Dir::make($this->tempFolder);
    $fileName = Misc::randString(10, true);
    copy($file['tmp_name'], $this->tempFolder.'/'.$fileName);
    db()->query('INSERT INTO upload_temp SET ?a', [
      'formId'     => $this->formId,
      'tempId'     => $this->tempId,
      'fieldName'  => $fieldName,
      'fileName'   => $fileName,
      'name'       => $file['name'],
      'multiple'   => $this->options['multiple'],
      'dateCreate' => dbCurTime()
    ]);
  }

  function delete() {
    Dir::remove($this->tempFolder);
    db()->query('DELETE FROM upload_temp WHERE tempId=?', $this->tempId);
  }

  function deleteFile($name) {
    $fileName = db()->selectCell('SELECT fileName FROM upload_temp WHERE tempId=? AND formId=? AND name=?', $this->tempId, $this->formId, $name);
    db()->query('DELETE FROM upload_temp WHERE fileName=?', $fileName);
    File::delete($this->tempFolder.'/'.$fileName);
  }

  static function extendFormOptions(Form $form, $uploadUrl = null) {
    if (!isset($form->fields)) throw new Exception('Call constructor first');
    $ut = new self([
      'formId'   => $form->id(),
      'multiple' => true
    ]);
    $files = Req::convertFiles($ut->getFiles());
    if (!empty($files)) $form->options['files'] = $files;
    if (!$uploadUrl) $uploadUrl = '/c2/uploadTemp';
    $uploadUrl = Misc::addParam($uploadUrl, 'formId', $form->id());
    $uploadUrl = Misc::addParam($uploadUrl, 'tempId', $ut->tempId);
    $uploadUrl = Misc::addParam($uploadUrl, 'fn', '{fn}');
    $form->options['uploadOptions'] = [
      'url'         => $uploadUrl,
      'loadedFiles' => Arr::filterByKeys2($files, ['name', 'size'])
    ];
    return $ut;
  }

  static function deleteOld() {
    $timeToKeepFiles = 60 * 60;
    foreach (Dir::getFilesR(TEMP_PATH.'/upload') as $file) if (filemtime($file) < time() - $timeToKeepFiles) File::delete($file);
    Dir::removeEmpties(TEMP_PATH.'/upload');
    db()->query('DELETE FROM upload_temp WHERE dateCreate < ?', dbCurTime(time() - $timeToKeepFiles));
    foreach (db()->select('SELECT * FROM upload_temp') as $v) if (!file_exists(TEMP_PATH.'/upload/'.$v['tempId'].'/'.$v['fileName'])) db()->query('DELETE FROM upload_temp WHERE tempId=? AND fileName=?', $v['tempId'], $v['fileName']);
  }

  static function cleanup() {
    Dir::clear(TEMP_PATH.'/upload');
    db()->query('TRUNCATE TABLE upload_temp');
  }

}
