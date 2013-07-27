<?php

class FilePatcher extends Patcher {
  
  protected function initPatchesFolders() {
    $this->patchesFolders[] = LIB_PATH.'/more/patcher/filePatches';
  }

  function getSiteLastPatchN() {
    return SiteConfig::getConstant('site', 'LAST_FILE_PATCH');
  }
  
  function updateSiteLastPatchN($n) {
    SiteConfig::updateConstant('site', 'LAST_FILE_PATCH', $n);
  }
  
}
