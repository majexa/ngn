<?php

class LongJobStates {

  public $collection;

  function __construct() {
    $this->collection = new LongJobStatesCollection;
  }

  function start($id) {
    LongJobCore::state($id)->start();
    $this->collection->add($id);
  }

  function delete($id) {
    LongJobCore::state($id)->delete();
    $this->collection->remove($id);
  }

  function destroy($forceStarting = false) {
    foreach ($this->collection as $state) {
      /* @var $state LongJobState */
      $state->delete($forceStarting);
      output2("job '{$state->id}' deleted. but status is: ".$state->status());
    }
    $this->collection->destroy();
  }

}