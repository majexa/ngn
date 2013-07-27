<?php

abstract class TinyUploadManager {

  protected $folderPath;
  protected $folder;
  
  function __construct($attachId) {
    Misc::checkEmpty($attachId);
    $this->folderPath = TinyAttachManager::getFolderPath($attachId);
    $this->folder = TinyAttachManager::getFolder($attachId);
  }
  
}
