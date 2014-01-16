<?php

class CtrlAdminDefault extends CtrlAdmin {
  
  protected function actionNotFound($actionMethod) {
  }

  function action_switchTestingMode() {
    $_SESSION['testing'] = (bool)$this->req->params[3];
    $this->redirect(Tt()->getPath(1));
  }
  
}
