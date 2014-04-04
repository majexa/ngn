<?php

class CtrlCommonUserSendEmail extends CtrlCommon {

  function action_json_default() {
    $toUser = DbModelCore::get('users', $this->req->rq('toUserId'));
    $this->json['title'] = 'Отправить сообщение '.$toUser['login'];
    $form =  new Form(new Fields([[
      'name' => 'text',
      'type' => 'textarea'
    ]]), [
      'submitTitle' => 'Отправить'
    ]);
    if ($form->isSubmittedAndValid()) {
      $pm = new PrivMsgs(Auth::get('id'));
      $data = $form->getData();
      $pm->sendMsg(Auth::get('id'), $toUser['id'], $data['text']);
      return;
    }
    return $form;
  }

}