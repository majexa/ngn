<?php

class CronJobUserRegPhoneCleanup extends CronJobAbstract {

  public $period = '5min';

  function __construct() {
    $this->enabled = Config::getVarVar('userReg', 'phoneConfirm', true);
  }

  function _run() {
    db()->query("DELETE FROM userPhoneConfirm WHERE dateCreate < ?", Date::db(time()-CtrlCommonUserRegPhone::expireTime()));
  }

}