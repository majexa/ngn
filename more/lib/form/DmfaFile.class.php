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
    try {
      $r = File::getExtension($el['postValue']['tmp_name']);
    } catch (NoFileException $e) {
      if ($el->options['name'] == 'sample2') {
        // тут всё не ок
        pr($el['postValue']['tmp_name'].': '.file_exists($el['postValue']['tmp_name']));
        //die2();
      }

      //die('-');
      throw new NoFileException('File "'.$el['postValue']['tmp_name'].'" for field "'.$el['name'].'" does not exists');
    }
    return $r;
  }

  function elAfterCreateUpdate(FieldEFile $el) {
    // Необходимо запускать постобработку только если есть "value", т.е. если загружен новый файл
    if (empty($el['postValue'])) return false;
    $attachFolder = $this->getAttacheFolder();
    $attachPath = $this->getAttachePath();
    Dir::make($attachPath);
    $filename = $this->getAttacheFilenameByEl($el).'.'.$this->getExt($el);
    //pr('remove '.$el['name'].'> '.$el['postValue']['tmp_name']);
    rename($el['postValue']['tmp_name'], $attachPath.'/'.$filename);
    $this->dm->_updateField($this->dm->id, $el['name'], $attachFolder.'/'.$filename);
    return $attachPath.'/'.$filename;
  }

}