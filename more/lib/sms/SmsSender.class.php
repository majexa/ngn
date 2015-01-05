<?php

class SmsSender {

  function send($phone, $msg) {
    //sendHeader();
    //ob_start();
    (new Smsc)->send_sms($phone, $msg);
    //print Misc::transit(ob_get_clean());
  }

}