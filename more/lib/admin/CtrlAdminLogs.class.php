<?php

class CtrlAdminLogs extends CtrlAdmin {

  static $properties = [
    'order' => 310,
    'onMenu' => true
  ];

  protected $logName;
  
  protected function init() {
    $this->d['logs'] = LogReader::logs();
    if (empty($this->d['logs'])) throw new Exception('There is no logs');
    if (isset($this->req->params[2]) and in_array($this->req->params[2], $this->d['logs'])) {
      $this->logName = $this->req->params[2];
    } else {
      $this->logName = Arr::first($this->d['logs']);
    }
  }

  function action_default() {
    $this->d['logName'] = $this->logName;
    $this->d['items'] = LogReader::get($this->logName);
    $this->d['tpl'] = 'logs/default';
  }
  
  function action_cleanup() {
    LogReader::cleanup($this->logName);
    $this->redirect();
  }
  
  function action_delete() {
    LogReader::delete($this->logName);
    $this->redirect();
  }
  
}

CtrlAdminLogs::$properties['title'] = Lang::get('adminModuleLogs');
