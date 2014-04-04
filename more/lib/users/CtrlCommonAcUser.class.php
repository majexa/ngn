<?php

class CtrlCommonAcUser extends CtrlCommon {

  function action_json_default() {
    $mask = $this->req->rq('mask');
    if ($mask[0] == '_') {
      $this->json = UsersCore::getSystemUsers();
      return;
    }
    $field = UsersCore::getDefaultField();
    $table = UsersCore::getDefaultTable();
    $this->json = db()->selectCol("SELECT id AS ARRAY_KEY, $field FROM $table WHERE $field LIKE ? ORDER BY id LIMIT 10", $mask.'%');
  }

}