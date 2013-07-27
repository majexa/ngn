<?php

class CtrlCommonUserRole extends CtrlCommon {

  function action_json_default() {
    return $this->jsonFormActionUpdate(new UserRoleForm(Auth::get('id')));
  }

}
