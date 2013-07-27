<?php

class CtrlAdminBackup extends CtrlAdmin {

  static $properties = [
    'title' => 'Резервные копии',
    'order' => 310,
    'onMenu' => true
  ];
  
  function action_default() {
    $this->d['items'] = CurrentSiteBackup::getList();
  }
  
  function action_restore() {
    CurrentSiteBackup::restore($this->req->rq('id'));
    $this->redirect($this->tt->getPath(2).'?a=restoreComplete');
  }
  
  function action_restoreComplete() {
  }
  
  function action_make() {
    CurrentSiteBackup::make();
    $this->redirect();
  }
  
  function action_delete() {
    CurrentSiteBackup::delete($this->req->rq('id'));
    $this->redirect();
  }

}