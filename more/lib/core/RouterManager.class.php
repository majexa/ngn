<?php

class RouterManager {
use Options;

  protected $req;

  function __construct($options = []) {
    $this->setOptions($options);
    $this->req = isset($this->options['req']) ? $this->options['req'] : O::get('Req');
  }
  
  function router() {
    if (isset($this->req->params[0]) and in_array($this->req->params[0], RouterScripts::prefixes())) {
      return O::di('RouterScripts', (['req' => $this->req]));
    }
    elseif (isset($this->req->params[0]) and ($this->req->params[0] == 'admin' or $this->req->params[0] == 'god')) {
      return new AdminRouter(['req' => $this->req]);
    }
    else {
      return $this->getDefaultRouter();
    }
  }

  protected function getDefaultRouter() {
    return O::di('DefaultRouter', ['req' => $this->req]);
  }

}