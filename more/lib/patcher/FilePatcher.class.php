<?php

class FilePatcher extends Patcher {

  protected $type = 'file';

  function getProjectCurrentPatchIds() {
    return ProjectState::get("{$this->type}PatchLastIds", true) ? : ['ngn' => 0];
  }

}