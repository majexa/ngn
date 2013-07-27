<?php

class WsClient {

  private $socket = null, $host, $port;

  function __construct($host, $port) {
    $this->host = $host;
    $this->port = $port;
  }

  function connect($strict = true) {
    $key1 = $this->generateRandomString(32);
    $key2 = $this->generateRandomString(32);
    $key3 = $this->generateRandomString(8, false, true);
    $header = "GET / HTTP/1.1\r\n";
    $header .= "Upgrade: WebSocket\r\n";
    $header .= "Connection: Upgrade\r\n";
    $header .= "Host: {$this->host}:{$this->port}\r\n";
    $header .= "Origin: http://{$this->host}\r\n";
    $header .= "Sec-WebSocket-Key1: $key1\r\n";
    $header .= "Sec-WebSocket-Key2: $key2\r\n";
    $header .= "\r\n";
    $header .= $key3;
    if (($this->socket = @fsockopen($this->host, $this->port, $errno, $errstr, 2)) === false) {
      if ($strict) throw new Exception("Can't connect to web socket server {$this->host}:{$this->port}");
      return false;
    }
    if ((@fwrite($this->socket, $header)) === false) {
      if ($strict) throw new Exception("Can't write to socket ($errno:$errstr)");
      return false;
    }
    $r = fread($this->socket, 1000);
    //if ((@freead($this->socket, $header)) === false) {
    return $this;
  }

  function __destruct() {
    print "\nDISTRUCT\n";
    if ($this->socket) fclose($this->socket);
  }

  function sendData($string) {
    if ((@fwrite($this->socket, "\x00".$string."\xff")) === false) {
      throw new Exception("Can't write to socket ($errno:$errstr)");
    }
    print "*** data sent: $string";
    //$wsData = fread($this->_Socket, 2000);
    //$retData = trim($wsData, "\x00\xff");
    //return $retData;
  }

  private function generateRandomString($length = 10, $addSpaces = true, $addNumbers = true) {
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!"§$%&/()=[]{}';
    $useChars = array();
    for ($i = 0; $i < $length; $i++) $useChars[] = $characters[mt_rand(0, strlen($characters) - 1)];
    if ($addSpaces === true) array_push($useChars, ' ', ' ', ' ', ' ', ' ', ' ');
    if ($addNumbers === true) array_push($useChars, rand(0, 9), rand(0, 9), rand(0, 9));
    shuffle($useChars);
    $randomString = trim(implode('', $useChars));
    $randomString = substr($randomString, 0, $length);
    return $randomString;
  }

}