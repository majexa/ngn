<?php

require VENDORS_PATH.'/ratchet/vendor/autoload.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class BasicWsServer implements MessageComponentInterface {

  static function autoload() {}

  protected $clients;

  protected function log($s) {
    print "* $s\n";
    LogWriter::str('wss', $s);
  }

  function __construct() {
    $this->log("Starting WebSocket server...");
    $this->clients = new \SplObjectStorage;
  }

  function onOpen(ConnectionInterface $conn) {
    $this->log("New client connected");
    $this->clients->attach($conn);
  }

  function onMessage(ConnectionInterface $from, $msg) {
    $this->log("New message received: $msg");
    foreach ($this->clients as $client) {
      if ($from !== $client) $client->send($msg);
    }
  }

  function onClose(ConnectionInterface $conn) {
    $this->log("Client connection closed");
    $this->clients->detach($conn);
  }

  function onError(ConnectionInterface $conn, \Exception $e) {
    $this->log("Error: ".$e->getMessage());
    if (gettype($e) == 'RuntimeException') {
      Err::logWarning($e);
      $conn->close();
      return;
    }
    Err::log($e);
    $conn->close();
  }

}