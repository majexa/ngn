<?php

class DmfaImagesPreview extends DmfaFile {

  function deleteAttaches($fieldName) {
    foreach (glob($this->getAttachePath().'/'.$this->dm->getAttacheFilename($fieldName).'*') as $file)
      File::delete($file);
  }

  function getAttacheFolder() {
    return $this->dm->getAttacheFolder();
  }

  function elBeforeCreateUpdate(FieldEFile $el) {
    if (empty($el['postValue'])) return;
    $this->dm->setDataValue($el['name'], '');
  }

  function elAfterCreateUpdate(FieldEFile $el) {
    // Необходимо запускать постобработку только если есть "value", т.е. если загружен новый файл
    if (empty($el['postValue'])) return false;
    $attachFolder = $this->getAttacheFolder();
    $attachPath = $this->getAttachePath();
    Dir::make($attachPath);
    $baseFileName = $this->dm->getAttacheFilename($el['name']);
    foreach ($el['postValue'] as $i => $file) {
      $fileName = $baseFileName.'_'.$i.'.'.File::getExtension($file['tmp_name']);
      rename($file['tmp_name'], $attachPath.'/'.$fileName);
      $this->dm->makeThumbs($attachPath.'/'.$fileName);
      $r[] = $attachFolder.'/'.$fileName;
    }
    $this->dm->_updateField($this->dm->id, $el['name'], $r);
  }

}