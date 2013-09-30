<?php

trait LongJobCtrl {

  /**
   * @return LongJobAbstract
   */
  abstract protected function getLongJob();

  function action_json_ljStart() {
    if (!Misc::isAdmin()) throw new AccessDenied;
    $longJob = $this->getLongJob();
    LongJobCore::run($longJob);
    $this->json = LongJobCore::state($longJob->id())->all();
  }

  function action_json_ljStatus() {
    if (!Misc::isAdmin()) throw new AccessDenied;
    $this->json = LongJobCore::state($this->getLongJob()->id())->all();
  }

  function action_ajax_ljDelete() {
    if (!Misc::isAdmin()) throw new AccessDenied;
    LongJobCore::state($this->getLongJob()->id())->delete();
    print LongJobCore::state($this->getLongJob()->id())->status();
  }

}