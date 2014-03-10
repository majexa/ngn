<?php

trait WsItemChanged {

  function __call($method, $args) {
    if (Misc::hasPrefix('_', $method)) {
      call_user_func_array([$this, $method], $args);
      return;
    }
    try {
      if (method_exists($this, '_'.$method)) call_user_func_array([$this, '_'.$method], $args);
      else new NoMethodException(__CLASS__.'::_'.$method);
    } catch (Exception $e) {
      Err::log($e);
    }
    //LogWriter::v('itemChangedCall', [$method, $args]);
    $this->sendWS($args);
  }

  function sendWS($args) {
    $id = (int)(is_array($args[0]) ? $args[0]['host'] : $args[0]);
    $config = Config::getVar('wss');
    try {
      $c = (new WsClient($config['host'], $config['port']))->connect();
      if ($c) $c->sendData(json_encode(['changed', $id])); // js command
      unset($c);
    } catch (Exception $e) {
      Err::log($e);
    }
  }

}