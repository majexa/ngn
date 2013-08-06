<?php

class CtrlCommonVkAuth extends CtrlCommon {

  protected function init() {
    Misc::checkEmpty(Config::getVarVar('userReg', 'vkAuthEnable'));
  }

  function action_ajax_exists() {
    $this->ajaxSuccess = DbModelCore::get('users', $this->req->rq('login'), 'login');
  }
  
  protected function checkHash() {
    if (md5(
      Config::getVarVar('vk', 'appId').
      $this->req->rq('uid').
      Config::getVarVar('vk', 'secKey')
    ) != $this->req->rq('hash')) throw new Exception('Hash error');
  }
  
  function action_ajax_reg() {
    $d = $this->req->p;
    $d['active'] = 1;
    $this->checkHash();
    $imageUrl = $d['image'];
    unset($d['image']);
    $userId = DbModelCore::create('users', $d, true);
    Auth::loginByLogin($this->req->p['login']);
    if (($page = DbModelCore::get('pages', 'myProfile', 'controller')) !== false) {
      $im = DdCore::getItemsManager($page['id'], [
        'staticId' => $userId * $page['id']
      ]);
      if (isset($im->form->fields->fields['image'])) {
        $tempFile = TEMP_PATH.'/'.Misc::randString(10);
        O::get('Curl')->copy($imageUrl, $tempFile);
        $im->create([
          'image' => [
            'tmp_name' => $tempFile
           ]
        ]);
      }
    }
    $this->ajaxSuccess = true;
  }
  
  function action_ajax_default() {
    $this->checkHash();
    $this->ajaxSuccess = Auth::loginByLogin($this->req->rq('login'));
  }

}