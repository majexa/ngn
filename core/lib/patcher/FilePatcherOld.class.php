<?php

class FilePatcherOld extends PatcherOld {

  protected $folderName = 'filePatches';
  
  function getSiteLastPatchN() {
    return ProjectConfig::getConstant('site', 'LAST_FILE_PATCH');
  }
  
  function updateSiteLastPatchN($n) {
    ProjectConfig::updateConstant('site', 'LAST_FILE_PATCH', $n);
  }
  
}
