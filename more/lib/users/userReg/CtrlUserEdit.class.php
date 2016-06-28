<?php

/**
 * Экшены для редактирования данных пользователя. Таких как логин, имейл, телефон
 */
class CtrlUserEdit extends CtrlBase {

  /**
   * Настройки
   *
   * @var array
   */
  protected $conf;

  /**
   * @var DbModelUsers
   */
  protected $user;

  protected function init() {
    parent::init();
    $this->conf = Config::getVar('userReg');
  }

  protected function initUser() {
    $this->user = DbModelCore::get('users', Auth::get('id'));
    if (!$this->user) throw new Error404('Authorize');
  }

  /**
   * @param $name
   * @return Form
   * @throws Exception
   */
  protected function userEditForm($name) {
    $title = ucfirst($name);
    if (empty($this->conf['allow'.$title.'Edit'])) throw new Exception($title.' change not allowed');
    $this->initUser();
    $method = "process".ucfirst($name)."EditForm";
    return $this->$method();
  }

  protected function wrapProcessForm($name) {
    $form = $this->userEditForm($name);
    if ($form->isSubmittedAndValid()) {
      $this->d['tpl'] = 'common/successMsg';
      return;
    }
    $this->d['tpl'] = 'common/form';
    $this->d['form'] = $form->html();
  }

  function action_editLogin() {
    if (empty($this->conf['loginEnable'])) throw new Exception('Login change not allowed');
    $this->setPageTitle('Изменение '.UserRegCore::getLoginTitle());
    $this->wrapProcessForm('login');
  }

  function action_editPass() {
    $this->wrapProcessForm('pass');
    $this->setPageTitle('Изменение пароля');
  }

  function action_editEmail() {
    $this->wrapProcessForm('email');
    $this->setPageTitle("Изменение e-mail'а");
  }

  function action_editPhone() {
    $this->wrapProcessForm('phone');
    $this->setPageTitle("Изменение телефона");
  }

  function action_json_editPhone() {
    $this->json['title'] = 'Телнфон';
    $form = $this->userEditForm('phone');
    $form->action = '/'.$this->req->path;
    return $form;
  }

  function action_json_editName() {
    $this->json['title'] = 'Имя';
    $form = $this->userEditForm('name');
    $form->action = '/'.$this->req->path;
    return $form;
  }

  protected function processFieldEditForm($fieldName, $fieldTitle, $fieldType = 'text') {
    $form = new UserFormPartial(new Fields([
      [
        'name'     => 'pass',
        'title'    => 'Ваш пароль',
        'type'     => 'password',
        'required' => true
      ],
      [
        'name'     => $fieldName,
        'title'    => $fieldTitle,
        'type'     => $fieldType,
        'required' => true
      ]
    ]));
    $form->options['submitTitle'] = 'Изменить';
    $form->setElementsData($this->user->getClean());
    if ($form->isSubmittedAndValid()) {
      if (!$form->getElement($fieldName)->valueChanged) {
        $form->globalError('Значение поля «'.$fieldTitle.'» не изменилось');
        return $form;
      }
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
    $form = $this->processFieldEditForm('login', UserRegCore::getLoginTitle());
    if ($form->isSubmittedAndValid()) {
      $data = $form->getData();
      Auth::loginByLogin($data['login']);
    }
    return $form;
  }

  protected function processPassEditForm() {
    $form = new Form(new Fields([
      [
        'name'     => 'curPass',
        'title'    => 'Текущий пароль',
        'type'     => 'password',
        'required' => true
      ],
      [
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
    return $this->processFieldEditForm('email', 'E-mail');
  }

  protected function processPhoneEditForm() {
    $form =  new UserEditPhoneForm;
    $form->setElementsData($this->user->getClean());
    return $form;
    //return $this->processFieldEditForm('phone', 'Телефон', 'phone');
  }

  protected function processNameEditForm() {
    return $this->processFieldEditForm('name', 'Имя');
  }

}
