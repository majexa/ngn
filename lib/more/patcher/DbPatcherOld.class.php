<?php

class DbPatcherOld extends PatcherOld {

  protected $folderName = 'dbPatches';

  function getSiteLastPatchN() {
    return SiteConfig::getConstant('site', 'LAST_DB_PATCH');
  }
  
  function updateSiteLastPatchN($n) {
    SiteConfig::updateConstant('site', 'LAST_DB_PATCH', $n);
  }

}