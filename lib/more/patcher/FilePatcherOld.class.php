<?php

class FilePatcherOld extends PatcherOld {

  protected $folderName = 'filePatches';
  
  function getSiteLastPatchN() {
    return SiteConfig::getConstant('site', 'LAST_FILE_PATCH');
  }
  
  function updateSiteLastPatchN($n) {
    SiteConfig::updateConstant('site', 'LAST_FILE_PATCH', $n);
  }
  
}
