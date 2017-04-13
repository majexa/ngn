<?php

trait CrudItemsCtrl {
  use CrudAbstractCtrl;

  function action_json_create() {
    $this->items()->create($this->req->r);
  }
  function action_json_update() {
    $this->items()->update($this->id(), $this->req->r);
  }
  function action_json_delete() {
    $this->items()->delete($this->id());
  }

}