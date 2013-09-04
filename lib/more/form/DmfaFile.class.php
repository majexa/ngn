<?php

class DmfaFile extends Dmfa {

  function deleteAttaches($fieldName) {
    foreach (glob($this->getAttachePath().'/'.$this->dm->getAttacheFilename($fieldName).'*') as $file)
      File::delete($file);
  }

  function getAttacheFolder() {
    return $this->dm->getAttacheFolder();
  }
  
  function getAttachePath() {
    return $this->dm->getAttachePath();
  }
  
  function getAttacheFilenameByEl(FieldEFile $el) {
    return $this->dm->getAttacheFilenameByEl($el);
  }
  
  function elBeforeCreateUpdate(FieldEFile $el) {
    if (empty($el['postValue'])) return;
    $this->dm->setDataValue($el['name'], '');
  }
  
  protected function getExt(FieldEFile $el) {
    return File::getExtension($el['postValue']['tmp_name']);
  }

  function elAfterCreateUpdate(FieldEFile $el) {
    // Необходимо запускать постобработку только если есть "value", т.е. если загружен новый файл
    if (empty($el['postValue'])) return false;
    $attachFolder = $this->getAttacheFolder();
    $attachPath = $this->getAttachePath();
    Dir::make($attachPath);
    $filename = $this->getAttacheFilenameByEl($el).'.'.$this->getExt($el);
    rename($el['postValue']['tmp_name'], $attachPath.'/'.$filename);
    $this->dm->items->updateField($this->dm->id, $el['name'], $attachFolder.'/'.$filename);
    return $attachPath.'/'.$filename;
  }

}