<?php

class CtrlCommonUserReg extends CtrlUserEdit {

  protected function getParamActionN() {
    return 2;
  }

  protected function init() {
    parent::init();
    $this->d['tpl'] = 'users/reg';
    Misc::checkEmpty($this->conf['enable'], 'Registration not enabled');
  }

  function action_rules() {
    $this->setPageTitle('Правила регистрации');
    $this->d['tpl'] = 'users/rules';
  }

  protected function getForm() {
    $form = new UserRegForm([
      'submitTitle'     => 'Готово',
      'defaultsFromReq' => true,
      'role'            => isset($this->req->r['role']) ? $this->req->r['role'] : null
    ]);
    $form->action = '/'.Sflm::frontendName(true).'/userReg/json_form';
    return $form;
  }

  function action_default() {
    if (Auth::get('id')) throw new Error404("Authorized. Can't register");
    $this->d['tpl'] = 'users/reg';
    $this->setPageTitle('Регистрация');
    $form = $this->getForm();
    $this->d['form'] = $form->html();
    if ($form->update()) {
//      $data = $form->getData();
//      if (empty($this->conf['activation']) and !empty($this->conf['authorizeAfterReg']))
//      Auth::loginByRequest($data['login'], $data['pass']);
      $this->redirect($this->tt->getPath(1).'/complete');
    }
  }

  function action_redirectFirstEdit() {
    $this->initSubmenu();
    $this->redirect($this->tt->getPath(1).'/'.$this->d['submenu'][0]['name']);
  }

  function action_json_form() {
    $form = $this->getForm();
    $form->options['onCreate'] = function($id) {
      Auth::loginById($id);
    };
    $this->json['title'] = 'Регистрация';
    if ($form->update()) {
      $this->json['success'] = true;
      $this->json['activation'] = $this->conf['activation'];
      if (empty($this->conf['activation']) and !empty($this->conf['authorizeAfterReg'])) {
        $this->json['authorized'] = true;
      }
      return null;
    }
    return $form;
  }

  /**
   * Страница с сообщением об успешной авторизации
   */
  function action_complete() {
    $this->isDefaultAction = false;
    $this->d['tpl'] = 'users/regComplete';
  }

  /**
   * Страница с сообщением об успешной регистрации
   */
  function action_welcome() {
    $this->isDefaultAction = false;
    $this->d['tpl'] = 'users/regWelcome';
  }

  protected function initSubmenu() {
    $items = [];
    if ($this->conf['loginEnable'] and $this->conf['allowLoginEdit']) {
      $items[] = [
        'title' => 'Изменить '.UserRegCore::getLoginTitle(),
        'link'  => $this->tt->getPath(1).'/editLogin',
        'name'  => 'editLogin'
      ];
    }
    if ($this->conf['allowPassEdit']) {
      $items[] = [
        'title' => 'Изменить пароль',
        'link'  => $this->tt->getPath(1).'/editPass',
        'name'  => 'editPass'
      ];
    }
    if ($this->conf['emailEnable'] and $this->conf['allowEmailEdit']) {
      $items[] = [
        'title' => 'Изменить e-mail',
        'link'  => $this->tt->getPath(1).'/editEmail',
        'name'  => 'editEmail'
      ];
    }
    if ($this->conf['phoneEnable'] and $this->conf['allowPhoneEdit']) {
      $items[] = [
        'title' => 'Изменить телфон',
        'link'  => $this->tt->getPath(1).'/editPhone',
        'name'  => 'editPhone'
      ];
    }
    if (Config::getVarVar('mysite', 'enable', true)) {
      if ($this->conf['allowNameEdit']) {
        $items[] = [
          'title' => 'Изменить домен',
          'link'  => $this->tt->getPath(1).'/editName',
          'name'  => 'editName'
        ];
      }
    }
//    $this->d['submenu'] = ::getLinks($items, $this->action);
//    foreach ($this->d['submenu'] as $v) {
//      if ($v['name'] == $this->action) {
//        $this->setPageTitle($v['title'], true);
//        break;
//      }
//    }
  }

}
