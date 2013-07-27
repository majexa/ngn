<?php

class CtrlAdminPrivMsgs extends CtrlAdmin {

  static $properties = [
    'title' => 'Сообщения',
    'onMenu' => true,
    'order' => 20
  ];
  
  function init() {
    if (!$this->oPM) throw new Exception('$this->oPM not defined');
    $this->d['tpl'] = 'privMsgs/default';
  }

  function action_default() {
    $this->d['msgs'] = $this->oPM->getAllMsgs();
  }
  
  function action_delete() {
    $this->oPM->deleteMsgs($this->userId, [$this->req->r['id']]);
    $this->redirect();
  }
  
  function action_clear() {
    $this->oPM->clearMsgs($this->userId);
    $this->redirect();
  }
  
  function action_send() {
    $this->oPM->sendMsg(Auth::get('id'), $this->req->r['user'], $this->req->r['text']);
    $this->redirect($this->tt->getPath(2).'/sendComplete');
  }
  
  function action_sendComplete() {
    $this->d['tpl'] = 'privMsgs/complete';
  }
  
  function action_sendPage() {
    $this->d['toUser'] = DbModelCore::get('users', $this->req->r['userId']);
    $this->d['tpl'] = 'privMsgs/send';
  }

}