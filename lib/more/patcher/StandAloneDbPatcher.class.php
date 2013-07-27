<?php

class StandAloneDbPatcher extends Patcher {
  
  private $siteFolder;
  
  function setSiteFolder($folder) {
    $this->siteFolder = $folder;
  }
  
  protected function initPatchesFolder() {
    // dummy
  }
  
  function getSiteLastPatchN() {
    return Config::getConstant($this->siteFolder.'/config/constants/more.php', 'LAST_DB_PATCH');
  }
  
  function updateSiteLastPatchN($n) {
    Config::replaceConstant($this->siteFolder.'/config/constants/more.php', 'LAST_DB_PATCH', $n);
  }
  
} 
