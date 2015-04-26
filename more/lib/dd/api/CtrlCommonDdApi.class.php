<?php

class CtrlCommonDdApi extends CtrlCommon {

  protected function getParamActionN() {
    return 3;
  }

  protected $items;

  protected function init() {
    $this->items = new DdItems($this->req->param(2));
    $this->items->cond->setLimit(10);
  }

  function action_json_list() {
    $this->json = array_values($this->items->getItems());
  }

}
