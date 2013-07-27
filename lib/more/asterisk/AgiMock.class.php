<?php

class AgiMock extends AgiBase {

  protected function openStreams() {
    $this->out = defined('STDOUT') ? STDOUT : fopen('php://stdout', 'w');
  }

  protected function readRequest() {
    $this->request = [
      'agi_request'      => 'agi.php',
      'agi_channel'      => 'SIP/sipnet-0000001f',
      'agi_language'     => 'en',
      'agi_type'         => 'SIP',
      'agi_uniqueid'     => '1361278264.31',
      'agi_version'      => '1.8.11.1-1digium1~squeeze',
      'agi_callerid'     => '+74666210227',
      'agi_calleridname' => 'unknown',
      'agi_callingpres'  => '0',
      'agi_callingani2'  => '0',
      'agi_callington'   => '0',
      'agi_callingtns'   => '0',
      'agi_dnid'         => 'unknown',
      'agi_rdnis'        => 'unknown',
      'agi_context'      => 'common',
      'agi_extension'    => 's',
      'agi_priority'     => '1',
      'agi_enhanced'     => '0.0',
      'agi_accountcode'  => '',
      'agi_threadid'     => '139940380980992',
    ];
  }

  protected $mockVars = [
    'project' => 'kp',
    'actionName' => 'kp'
  ];

  function getVar($k) {
    return $this->mockVars[$k];
  }

  function conlog($str, $vbl = 1) {
    print "\n$str";
  }

}