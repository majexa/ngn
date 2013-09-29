<?php

class FilePatcher extends Patcher {

  protected $type = 'file';

  function getProjectCurrentPatchIds() {
    return Config::getVar("{$this->type}PatchLastIds", true) ? : ['ngn' => 0];
  }


}