<?php

trait DdCrudAuthorCtrl {

  protected function processItems(DdItems $items) {
    $items->getNonActive = true;
    if (!Misc::isAdmin()) $items->cond->addF('userId', Auth::get('id'));
    return $items;
  }

}
