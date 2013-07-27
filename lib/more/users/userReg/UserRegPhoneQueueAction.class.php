<?php

class UserRegPhoneQueueAction {

  function received(array $d) {
    if (!$r = db()->getRow('userPhoneConfirm', $d['id'])) return;
    if ($r['code'] != $d['code']) return;

  }

}