<?php

class CtrlCommonUserStorage extends CtrlCommon {

  function action_json_getAll() {

  }

  function action_json_get() {
    $this->json = UsersSettings::get($this->req->param(2));
  }

  function action_json_set() {
    foreach ($this->req['data'] as $k => $v) UsersSettings::set($k, $v);
  }

}