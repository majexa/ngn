<?php

class EmailAdmin {

  static function send($subject, $message, $html = true) {
    $o = new SendEmail();
    foreach (Config::getVar('admins') as $id) {
      if (($user = DbModelCore::get('users', $id)) === false) continue;
      $o->send($user['email'], $subject, $message, $html);
    }
  }
	
}