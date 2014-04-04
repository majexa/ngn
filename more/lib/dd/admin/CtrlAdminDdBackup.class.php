<?php

class CtrlAdminDdBackup extends CtrlAdmin {

  static $properties = [
    'title' => 'Резервное копирование',
    'descr' => '',
    'onMenu' => false
  ];

  function action_make() {
    DdBackup::make();
  }
 
  function action_restore() {
    DdBackup::restore();
    $this->redirect();
  }

}