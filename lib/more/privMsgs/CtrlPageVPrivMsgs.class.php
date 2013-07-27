<?php

//function json_encode2($data) {
//  return json_encode(Misc::iconvR(CHARSET, 'UTF-8', $data));
//}

class CtrlPageVPrivMsgs extends CtrlPage {
  
  /**
   * @var PrivMsgs
   */
  public $privMsgs;
  
  public $userId;
  public $toUserId;
  public $toUserData;  
  public $mainLayout = true;
  public $moduleName = 'priv_msgs';
  
  function init() {
    die2(22);
    $this->userId = Auth::get('id');
    $this->d['userData'] = DbModelCore::get('users', $this->userId);
    //$this->req->r += parse_query();
    if ($toUserId = (int)$this->req->params[1]) {
      if ($this->toUserData = $this->getUserData($toUserId)) {
        if ($this->toUserData['image_sm']) {
          //$this->toUserData['image_sm'] = $_CONFIG_SITE['user_photo_dir'].'/'.$this->toUserData['image_sm'];
        }
        $this->toUserId = $toUserId;
      }
    }
  }
  
  function action_default() {
    //if (!$this->mainLayout) return;
    $this->d['userId'] = $this->userId;
    if ($this->toUserData) {
      $this->d['toUserData'] = $this->toUserData;
      $this->d['toUserId'] = $this->toUserId;
    } else {
      if ($this->userId) {
        $this->d['users'] = $this->getContacts($this->userId);
        $this->setFriends();
        $this->d['friendsQueue'] = $this->friends->getQueue($this->userId);
      }
    }
    $this->d['tpl'] = 'privMsgs/main';
  }
  
  function setMsgsObj() {
    if (!$this->userId) return false;
    $this->privMsgs = new PrivMsgs($this->userId, '');
    return true;
  }
  
  function action_history() {
    if (!$this->setMsgsObj()) return;
    if ($this->toUserData) {
      $this->d['userId'] = $this->userId;
      $this->d['toUserId'] = $this->toUserId;
      $this->d['toUserData'] = $this->toUserData;
      $this->d['items'] = $this->privMsgs->getHistory($this->userId, $this->toUserId);
    }
    $this->d['tpl'] = 'privMsgs/history';
    $this->mainLayout = false;
  }

  function action_ajaxSend() {
    $this->hasOutput = false;
    if (!$this->setMsgsObj()) return;
    if (!$_POST['text']) return;
    $this->privMsgs->sendMsg($this->userId, $this->req->r['toUserId'], $_POST['text']);
  }
  
  function action_ajaxGetMsgs() {
    $this->hasOutput = false;
    if (!$this->setMsgsObj()) return;
    if (!$this->toUserId) return;    
    $isHistory = false;
    if (!$msgs = $this->privMsgs->getMsgs($this->userId, $this->toUserId, false)) {
      if ($this->privMsgs->getHistory($this->userId, $this->toUserId)) {
        $isHistory = true;
      }
    }    
    $this->tt->tpl('privMsgs/msgList.ajax', [
      'userId' => $this->userId,
      'toUserId' => $this->toUserId,
      'items' => $msgs,
      'isHistory' => $isHistory
    ], $this->moduleName);
  }
  
  function action_ajaxGetHistory() {
    $this->hasOutput = false;
    if (!$this->setMsgsObj()) return;
    if (!$this->toUserId) return;    
    $isHistory = false;
    $msgs = $this->privMsgs->getHistory($this->userId, $this->toUserId);
    $this->tt->tpl('privMsgs/msgListHistory', [
      'userId' => $this->userId,
      'toUserId' => $this->toUserId,
      'items' => $msgs
    ], $this->moduleName);
  }
  
  function action_ajaxGetNewMsgs() {
    $this->hasOutput = false;
    if (!$this->setMsgsObj()) return;
    if (!$this->toUserData) return false;
    // Важно не допускать вывода шаблона, если записей нет, т.к. нужно вернуть пустую строку
    if (!$msgs = $this->privMsgs->getMsgs($this->userId, $this->toUserId, true)) {
      print 'null';
      return;
    }
    $isNewMsgs = false;
    foreach ($msgs as $k => $v) if ($v['userId'] != $this->userId) $isNewMsgs = true;
    print json_encode2([
      'isNewMsgs' => $isNewMsgs,
      'msgs' => $this->tt->getTpl('privMsgs/msgList.ajax', [
        'userId' => $this->userId,
        'items' => &$msgs
      ], $this->moduleName)
    ]);
  }
  
  function action_ajaxDeleteChat() {
    $this->hasOutput = false;
    if (!$this->setMsgsObj()) return;
    if (!$this->toUserId) return;
    $this->privMsgs->deleteChat($this->userId, $this->toUserId);
  }
  
  function action_ajaxDeleteMsgs() {
    $this->hasOutput = false;
    if (!$this->setMsgsObj()) return;
    $this->privMsgs->deleteMsgs($this->userId, $_POST['msgIds']);
  }

}
