<?php

class CtrlCommonUserReg extends CtrlCammon {

  /**
   * Настройки
   *
   * @var array
   */
  private $conf;

  protected function init() {
    parent::init();
    $this->d['tpl'] = 'users/reg';
    $this->conf = Config::getVar('userReg');
    Misc::checkEmpty($this->conf['enable'], 'Registration not enabled');
  }

  function action_rules() {
    $this->setPageTitle('Правила регистрации');
    $this->d['tpl'] = 'users/rules';
  }

  protected function getForm() {
    $form =  new UsersRegForm([
      'submitTitle'     => 'Зарегистрироваться',
      'defaultsFromReq' => true,
      'role'            => isset($this->req->r['role']) ? $this->req->r['role'] : null
    ]);
    $form->action = '/c/userReg/json_form';
    return $form;
  }

  function action_default() {
    if (Auth::get('id')) {
      $this->error404('Ошибка', 'Вы авторизованы и не можете регистрироваться');
      return;
    }
    $this->d['tpl'] = 'users/reg';
    $this->setPageTitle('Регистрация');
    $form = $this->getForm();
    $this->d['form'] = $form->html();
    if ($form->update()) {
      //$data = $form->getData();
      //if (empty($this->conf['activation']) and !empty($this->conf['authorizeAfterReg']))
      //Auth::loginByRequest($data['login'], $data['pass']);
      $this->redirect($this->path->getPath(1).'/complete');
    }
  }

  function action_redirectFirstEdit() {
    $this->initSubmenu();
    $this->redirect($this->path->getPath(1).'/'.$this->d['submenu'][0]['name']);
  }

  function action_json_form() {
    $form = $this->getForm();
    $this->json['title'] = 'Регистрация';
    if ($form->update()) {
      $this->json['success'] = true;
      if (empty($this->conf['activation']) and !empty($this->conf['authorizeAfterReg'])) {
        $this->json['authorized'] = true;
        Auth::loginByRequest($this->conf['loginEnable'] ? $form->elementsData['login'] : $form->elementsData['email'], $form->elementsData['pass']);
        return;
      }
      $this->json['activation'] = $this->conf['activation'];
      return;
    }
    return $this->jsonFormAction($form);
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

  function action_activation() {
    if (!$this->page['settings']['activation']) return;
    $this->d['tpl'] = 'users/activation';
    $this->d['success'] = UsersActivation::activate($this->req->r['code']);
    $this->redirect($this->path->getPath().'/welcome');
  }

  // ----------------------------------------------------

  protected function initSubmenu() {
    $items = [];
    if ($this->conf['loginEnable'] and $this->conf['allowLoginEdit']) {
      $items[] = [
        'title' => 'Изменить '.UserRegCore::getLoginTitle(),
        'link'  => $this->path->getPath(1).'/editLogin',
        'name'  => 'editLogin'
      ];
    }
    if ($this->conf['allowPassEdit']) {
      $items[] = [
        'title' => 'Изменить пароль',
        'link'  => $this->path->getPath(1).'/editPass',
        'name'  => 'editPass'
      ];
    }
    if ($this->conf['emailEnable'] and $this->conf['allowEmailEdit']) {
      $items[] = [
        'title' => 'Изменить e-mail',
        'link'  => $this->path->getPath(1).'/editEmail',
        'name'  => 'editEmail'
      ];
    }
    if ($this->conf['phoneEnable'] and $this->conf['allowPhoneEdit']) {
      $items[] = [
        'title' => 'Изменить телфон',
        'link'  => $this->path->getPath(1).'/editPhone',
        'name'  => 'editPhone'
      ];
    }
    if (Config::getVarVar('mysite', 'enable')) {
      if ($this->conf['allowNameEdit']) {
        $items[] = [
          'title' => 'Изменить домен',
          'link'  => $this->path->getPath(1).'/editName',
          'name'  => 'editName'
        ];
      }
      if ($this->conf['allowMysiteThemeEdit']) {
        $items[] = [
          'title' => 'Оформление Моего сайта',
          'link'  => $this->path->getPath(1).'/editMysiteTheme',
          'name'  => 'editMysite'
        ];
      }
    }
    $this->d['submenu'] = getLinks($items, $this->action);
    foreach ($this->d['submenu'] as $v) {
      if ($v['name'] == $this->action) {
        $this->setPageTitle($v['title'], true);
        break;
      }
    }
  }

  /**
   * @var DbModelUsers
   */
  protected $user;

  protected function initUser() {
    $this->user = DbModelCore::get('users', Auth::get('id'));
    if (!$this->user) {
      $this->error404('Авторизуйтесь');
      return false;
    }
    return true;
  }

  protected function wrapProcessForm($name) {
    if (!$this->initUser()) return;
    $this->initSubmenu();
    $method = "process".ucfirst($name)."EditForm";
    $form = $this->$method();
    if ($form->isSubmittedAndValid()) {
      $this->d['tpl'] = 'common/successMsg';
      return;
    }
    $this->d['tpl'] = 'common/form';
    $this->d['form'] = $form->html();
  }

  function action_editLogin() {
    if (empty($this->conf['loginEnable']) or empty($this->conf['allowLoginEdit'])) throw new Exception('Login change not allowed');
    $this->setPageTitle('Изменение '.UserRegCore::getLoginTitle());
    $this->wrapProcessForm('login');
  }

  function action_editPass() {
    if (empty($this->conf['allowPassEdit'])) throw new Exception('Password change not allowed');
    $this->wrapProcessForm('pass');
    $this->setPageTitle('Изменение пароля');
  }

  function action_editEmail() {
    if (empty($this->conf['allowEmailEdit'])) throw new Exception('Email change not allowed');
    $this->wrapProcessForm('email');
    $this->setPageTitle("Изменение e-mail'а");
  }

  function action_editPhone() {
    if (empty($this->conf['allowPhoneEdit'])) throw new Exception('Phone change not allowed');
    $this->wrapProcessForm('phone');
    $this->setPageTitle("Изменение телефона");
  }

  function action_editName() {
    if (!Config::getVarVar('mysite', 'enable')) throw new Exception('Mysite is disabled');
    if (empty($this->conf['allowNameEdit'])) throw new Exception('Name change not allowed');
    $this->wrapProcessForm('name');
    $this->setPageTitle("Изменение e-mail'а");
  }

  function action_editMysiteTheme() {
    if (!Config::getVarVar('mysite', 'enable')) throw new Exception('Mysite is disabled');
    if (empty($this->conf['allowMysiteThemeEdit'])) throw new Exception('MysiteTheme change not allowed');
    $this->d['tpl'] = 'users/regEdit';
    if (!$this->initUser()) return;
    $this->initSubmenu();
    $this->processMysiteThemeForm();
  }

  protected function processFieldEditForm($fieldName, $fieldTitle, $fieldType = 'text') {
    $form = new Form(new Fields([
      [
        'name'     => 'pass',
        'title'    => 'Ваш пароль',
        'type'     => 'password',
        'required' => true
      ], [
        'name'     => $fieldName,
        'title'    => $fieldTitle,
        'type'     => $fieldType,
        'required' => true
      ]
    ]));
    $form->options['submitTitle'] = 'Изменить';
    $form->setElementsData($this->user->getClean());
    if ($form->isSubmittedAndValid()) {
      $data = $form->getData();
      if (!$this->user->checkPass($data['pass'])) $form->globalError('Ваш пароль введён неверно');
      elseif (DbModelCore::get('users', $data[$fieldName], $fieldName)) $form->globalError("Такой $fieldTitle уже существует");
      else {
        DbModelCore::update('users', Auth::get('id'), [$fieldName => $data[$fieldName]]);
      }
    }
    return $form;
  }

  protected function processLoginEditForm() {
    $oF = $this->processFieldEditForm('login', UserRegCore::getLoginTitle());
    if ($oF->isSubmittedAndValid()) {
      $data = $oF->getData();
      Auth::loginByLogin($data['login']);
    }
    return $oF;
  }

  protected function processPassEditForm() {
    $form = new Form(new Fields([
      [
        'name'     => 'curPass',
        'title'    => 'Текущий пароль',
        'type'     => 'password',
        'required' => true
      ], [
        'name'     => 'newPass',
        'title'    => 'Новый пароль',
        'type'     => 'password',
        'required' => true
      ],
    ]));
    $form->options['submitTitle'] = 'Изменить';
    $form->setElementsData();
    if ($form->isSubmittedAndValid()) {
      $data = $form->getData();
      if (!$this->user->checkPass($data['curPass'])) $form->getElement('curPass')->error('Текущий пароль введён неверно');
      else
        DbModelCore::update('users', $this->user['id'], ['pass' => $data['newPass']]);
    }
    return $form;
  }

  protected function processEmailEditForm() {
    return $this->processFieldEditForm('email', 'e-mail');
  }

  protected function processPhoneEditForm() {
    return $this->processFieldEditForm('phone', 'телефон', 'phone');
  }

  protected function processNameEditForm() {
    return $this->processFieldEditForm('name', 'домен');
  }

  protected function processMysiteThemeForm() {
    $folder = UPLOAD_PATH.'/mysite/'.$this->user['id'];
    $file = $folder.'/bg.jpg';
    $this->d['fields'] = $fields = [
      [
        'name'     => 'image',
        'title'    => 'Картинка для шапки',
        'type'     => 'image',
        'required' => true,
        'default'  => file_exists($file) ? UPLOAD_DIR.'/mysite/'.$this->user['id'].'/bg.jpg' : ''
      ],
    ];
    $oF = new Form(new Fields($fields));
    $oF->options['submitTitle'] = 'Изменить';
    $data = $oF->setElementsData();
    if ($oF->isSubmittedAndValid()) {
      Dir::make($folder);
      copy($data['image']['tmp_name'], $file);
      unlink($data['image']['tmp_name']);
    }
    $this->d['form'] = $oF->html();
  }

  function action_deleteFile() {
    if (!Config::getVarVar('mysite', 'enable')) throw new Exception('Mysite is disabled');
    if (empty($this->conf['allowMysiteThemeEdit'])) throw new Exception('MysiteTheme change not allowed');
    if (!$this->initUser()) return;
    if (file_exists(UPLOAD_PATH.'/mysite/'.$this->user['id'].'/bg.jpg')) unlink(UPLOAD_PATH.'/mysite/'.$this->user['id'].'/bg.jpg');
    $this->redirect($this->path->getPath(1).'/editMysiteTheme');
  }

  function action_updateUserDataPageId() {
    db()->query("UPDATE users SET userDataPageId=?d WHERE id=?d", $this->req->r['userDataPageId'], Auth::get('id'));
    $this->redirect();
  }

}
