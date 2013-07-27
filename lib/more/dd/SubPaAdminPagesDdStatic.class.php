<?php

class SubPaAdminPagesDdStatic extends SubPaAdminPagesDd {

  function init() {
    parent::init();
    if (!isset($this->ctrl->req->r['a'])) $this->ctrl->redirect(Tt()->getPath().'?a=edit');
  }

}