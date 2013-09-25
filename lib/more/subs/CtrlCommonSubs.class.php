<?php

class FieldESdf extends FieldETypoText {
  protected function validate2() {
    if ($this->isPostValue)
      $this->error = 'Пользователь с таким логином уже существует';
  }
}

class CtrlCommonSubs extends CtrlCommon {

  function action_json_asd() {
    return new Form(new Fields([[
      'title' => 'Asd',
      'type' => 'sdf'
    ]]));
  }
  
  function action_default() {
    if (!isset($this->req->r['code']))
      throw new Exception("\$this->req->r['code'] not defined");
    if (!isset($this->req->r['subsId']))
      throw new Exception("\$this->req->r['subsId'] not defined");
    if (!isset($this->req->r['type']))
      throw new Exception("\$this->req->r['type'] not defined");
    new SubsReturn($this->req->r['subsId'], $this->req->r['code'], $this->req->r['type']);
    $this->redirect($this->req->r['link']);
  }

  function action_unsubscribe() {
    if (!isset($this->req->r['listId']))
      throw new Exception("\$this->req->r['listId'] not defined");
    if (!isset($this->req->r['code']))
      throw new Exception("\$this->req->r['code'] not defined");
    if (!isset($this->req->r['type']))
      throw new Exception("\$this->req->r['type'] not defined");
    if ($this->req->r['type'] == 'emails') {
      $r = db()->selectRow('SELECT * FROM subs_emails WHERE listId=?d AND code=?',
        $this->req->r['listId'], $this->req->r['code']);
      if (!$r) return;
      db()->query('DELETE FROM subs_emails WHERE listId=?d AND code=?',
        $this->req->r['listId'], $this->req->r['code']);
      LogWriter::str('unsubscribeEmails', $r['email']);
    } elseif ($this->req->r['type'] == 'users') {
      $r = db()->selectCell('
      SELECT subs_users.userId FROM subs_users, users
      WHERE
        subs_users.userId=users.id AND
        subs_users.listId=?d AND
        users.actCode=?',
      $this->req->r['listId'], $this->req->r['code']);
      if (!$r) return;
      db()->query('DELETE FROM subs_users WHERE listId=?d AND userId=?',
        $this->req->r['listId'], $r);
      LogWriter::str('unsubscribeUsers', $r);
    } else {
      throw new Exception('Type "'.$this->req->r['type'].'" does not exists');
    }
    $this->hasOutput = false;
    $subsListTitle = db()->selectCell(
      'SELECT title FROM subsList WHERE id=?d', $this->req->r['listId']);
    print 'Вы успешно отписаны от рассылки «'.$subsListTitle.'»';
  }

}