<?php

class RouterManager {
  use Options;

  protected $req;

  protected function defineOptions() {
    return [
      'routerOptions' => []
    ];
  }

  function __construct($options = []) {
    $this->setOptions($options);
    $this->req = isset($this->options['req']) ? $this->options['req'] : O::get('Req');
    $this->options['routerOptions']['req'] = $this->req;
  }

  function router() {
    if (isset($this->req->params[0]) and in_array($this->req->params[0], RouterScripts::prefixes())) {
      return O::di('RouterScripts', $this->options['routerOptions']);
    }
    elseif (isset($this->req->params[0]) and ($this->req->params[0] == 'admin' or $this->req->params[0] == 'god')) {
      return new AdminRouter($this->options['routerOptions']);
    }
    else {
      return $this->getDefaultRouter();
    }
  }

  protected function getDefaultRouter() {
    return O::di('DefaultRouter', $this->options['routerOptions']);
  }

}